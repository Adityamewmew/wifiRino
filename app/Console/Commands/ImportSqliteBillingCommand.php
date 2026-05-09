<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PDO;

class ImportSqliteBillingCommand extends Command
{
    protected $signature = 'billing:import-sqlite
                            {path? : Path ke file billing.db}
                            {--force : Tanpa konfirmasi; kosongkan tabel MySQL lalu impor}';

    protected $description = 'Salin data dari SQLite (backend Node lama) ke database MySQL billing_rinonet';

    /** @var list<string> */
    private array $importOrder = [
        'users',
        'areas',
        'paket',
        'pelanggan',
        'mikrotik_routers',
        'pelanggan_mikrotik',
        'tagihan_bulanan',
        'pembukuan',
        'audit_logs',
        'pengaturan',
        'pengumuman',
        'push_tokens',
        'push_dispatch_logs',
        'pending_delete_requests',
        'tugas_teknisi',
        'chat_threads',
        'chat_messages',
        'chat_staff_participants',
    ];

    public function handle(): int
    {
        $path = $this->argument('path') ?: $this->resolveSqlitePath();
        if ($path === null || ! is_readable($path)) {
            $this->error('File SQLite tidak ditemukan. Letakkan billing.db di ../server/billing.db atau beri argumen path penuh.');

            return self::FAILURE;
        }

        if (config('database.default') !== 'mysql') {
            $this->warn('DB_CONNECTION bukan mysql. Lanjut tetap memakai koneksi default.');
        }

        if (! $this->option('force')) {
            if (! $this->confirm('Ini akan MENGHAPUS semua data di tabel billing di MySQL lalu mengisi dari SQLite. Lanjut?', false)) {
                return self::INVALID;
            }
        }

        $pdo = new PDO('sqlite:'.$path, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        $this->info('Sumber: '.$path);

        Schema::disableForeignKeyConstraints();
        try {
            foreach (array_reverse($this->importOrder) as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                }
            }

            foreach ($this->importOrder as $table) {
                if (! Schema::hasTable($table)) {
                    continue;
                }
                if (! $this->sqliteTableExists($pdo, $table)) {
                    $this->line("Lewati <comment>{$table}</comment> (tidak ada di SQLite)");

                    continue;
                }
                $rows = $this->fetchAllSqlite($pdo, $table);
                if ($rows === []) {
                    $this->line("Kosong: {$table}");

                    continue;
                }
                $mysqlCols = array_flip(Schema::getColumnListing($table));
                $normalized = [];
                foreach ($rows as $r) {
                    $r = $this->normalizeRow($table, $r);
                    $normalized[] = array_intersect_key($r, $mysqlCols);
                }
                foreach (array_chunk($normalized, 200) as $chunk) {
                    DB::table($table)->insert($chunk);
                }
                $this->info(sprintf('%s: %d baris', $table, count($normalized)));
            }
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        $this->info('Selesai. Periksa aplikasi dan login dengan akun dari database lama.');

        return self::SUCCESS;
    }

    private function resolveSqlitePath(): ?string
    {
        $candidates = [
            storage_path('app/billing.db'),
            database_path('billing.db'),
            base_path('../server/billing.db'),
        ];
        foreach ($candidates as $p) {
            if (is_readable($p)) {
                return $p;
            }
        }

        return null;
    }

    private function sqliteTableExists(PDO $pdo, string $table): bool
    {
        $st = $pdo->prepare("SELECT 1 FROM sqlite_master WHERE type='table' AND name=?");
        $st->execute([$table]);

        return (bool) $st->fetchColumn();
    }

    /** @return list<array<string, mixed>> */
    private function fetchAllSqlite(PDO $pdo, string $table): array
    {
        $stmt = $pdo->query('SELECT * FROM '.$this->quoteIdent($table));
        $out = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $out ?: [];
    }

    private function quoteIdent(string $name): string
    {
        return '"'.str_replace('"', '""', $name).'"';
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    private function normalizeRow(string $table, array $row): array
    {
        foreach ($row as $k => $v) {
            if ($v === '') {
                $row[$k] = null;
            }
        }

        if ($table === 'chat_threads' && array_key_exists('delegatedToJson', $row)) {
            $raw = $row['delegatedToJson'];
            if ($raw === null || $raw === '') {
                $row['delegatedToJson'] = '[]';
            } elseif (is_string($raw)) {
                $dec = json_decode($raw, true);
                $row['delegatedToJson'] = json_encode(is_array($dec) ? $dec : [], JSON_UNESCAPED_UNICODE);
            } elseif (is_array($raw)) {
                $row['delegatedToJson'] = json_encode($raw, JSON_UNESCAPED_UNICODE);
            }
        }

        $tinyIntCols = [
            'users' => ['aktif'],
            'paket' => ['aktif'],
            'pengumuman' => ['aktif'],
            'push_tokens' => ['isActive'],
            'tugas_teknisi' => ['isBroadcast'],
            'chat_threads' => ['fieldPublicCanReply'],
        ];
        if (isset($tinyIntCols[$table])) {
            foreach ($tinyIntCols[$table] as $col) {
                if (array_key_exists($col, $row) && $row[$col] !== null) {
                    $row[$col] = (int) $row[$col];
                }
            }
        }

        if ($table === 'tagihan_bulanan') {
            foreach (['bulan', 'tahun'] as $c) {
                if (array_key_exists($c, $row) && $row[$c] !== null) {
                    $row[$c] = (int) $row[$c];
                }
            }
        }

        if ($table === 'mikrotik_routers' && array_key_exists('apiPort', $row) && $row['apiPort'] !== null) {
            $row['apiPort'] = (int) $row['apiPort'];
        }

        if ($table === 'push_dispatch_logs') {
            foreach (['successCount', 'failedCount'] as $c) {
                if (array_key_exists($c, $row) && $row[$c] !== null) {
                    $row[$c] = (int) $row[$c];
                }
            }
        }

        if ($table === 'mikrotik_routers' && array_key_exists('lastProbeMs', $row) && $row['lastProbeMs'] !== null) {
            $row['lastProbeMs'] = (int) $row['lastProbeMs'];
        }

        if ($table === 'mikrotik_routers' && array_key_exists('lastProbeOk', $row) && $row['lastProbeOk'] !== null) {
            $row['lastProbeOk'] = (int) $row['lastProbeOk'];
        }

        foreach ($row as $k => $v) {
            if (is_string($v) && $v !== '' && preg_match('/^\d{4}-\d{2}-\d{2}T/', $v)) {
                try {
                    $row[$k] = Carbon::parse($v)->format('Y-m-d H:i:s');
                } catch (\Throwable) {
                    /* biarkan */
                }
            }
        }

        return $row;
    }
}
