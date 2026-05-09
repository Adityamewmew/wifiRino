<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── Create tagihan bulanan for May 2026 ──
        $pelanggan = DB::table('pelanggan')->where('status', '!=', 'berhenti')->get();
        foreach ($pelanggan as $p) {
            $exists = DB::table('tagihan_bulanan')
                ->where('idPelanggan', $p->idPelanggan)
                ->where('bulan', 5)
                ->where('tahun', 2026)
                ->exists();
            if ($exists) {
                continue;
            }
            DB::table('tagihan_bulanan')->insert([
                'id' => (string) Str::uuid(),
                'idPelanggan' => $p->idPelanggan,
                'namaPelanggan' => $p->nama,
                'area' => $p->area,
                'paket' => $p->paket,
                'noWA' => $p->noWA,
                'bulan' => 5,
                'tahun' => 2026,
                'totalTagihan' => $p->totalFinal ?? $p->hargaPaket,
                'status' => 'belum_bayar',
                'tglJatuhTempo' => '2026-05-28 00:00:00',
                'createdAt' => now()->format('Y-m-d H:i:s'),
            ]);
        }

        // ── Mark 2 pelanggan as "lunas" (paid) ──
        $first2 = DB::table('tagihan_bulanan')
            ->where('bulan', 5)->where('tahun', 2026)
            ->limit(2)->pluck('id');
        foreach ($first2 as $tid) {
            DB::table('tagihan_bulanan')->where('id', $tid)->update([
                'status' => 'lunas',
                'tglBayar' => now()->format('Y-m-d H:i:s'),
                'metodeBayar' => 'tunai',
                'dibayar_ke' => 'admin@sans-speed.local',
            ]);
        }

        // ── Add pembukuan entry for the 2 lunas ──
        $lunasRows = DB::table('tagihan_bulanan')->where('status', 'lunas')->where('bulan', 5)->where('tahun', 2026)->get();
        foreach ($lunasRows as $row) {
            $exists = DB::table('pembukuan')->where('idReferensi', $row->id)->exists();
            if ($exists) {
                continue;
            }
            DB::table('pembukuan')->insert([
                'id' => (string) Str::uuid(),
                'tanggal' => now()->format('Y-m-d H:i:s'),
                'jenis' => 'pemasukan',
                'kategori' => 'Tagihan Internet',
                'nominal' => $row->totalTagihan,
                'keterangan' => 'Pembayaran tagihan ' . ($row->namaPelanggan ?? '') . ' (' . ($row->idPelanggan ?? '') . ') bulan 5/2026',
                'idReferensi' => $row->id,
                'createdBy' => DB::table('users')->where('role', 'owner')->value('id'),
                'createdAt' => now()->format('Y-m-d H:i:s'),
            ]);
        }

        // ── Also add April 2026 tagihan (all lunas) for trend data ──
        foreach ($pelanggan as $p) {
            $exists = DB::table('tagihan_bulanan')
                ->where('idPelanggan', $p->idPelanggan)
                ->where('bulan', 4)
                ->where('tahun', 2026)
                ->exists();
            if ($exists) {
                continue;
            }
            $tagId = (string) Str::uuid();
            DB::table('tagihan_bulanan')->insert([
                'id' => $tagId,
                'idPelanggan' => $p->idPelanggan,
                'namaPelanggan' => $p->nama,
                'area' => $p->area,
                'paket' => $p->paket,
                'noWA' => $p->noWA,
                'bulan' => 4,
                'tahun' => 2026,
                'totalTagihan' => $p->totalFinal ?? $p->hargaPaket,
                'status' => 'lunas',
                'tglJatuhTempo' => '2026-04-28 00:00:00',
                'tglBayar' => '2026-04-25 10:00:00',
                'metodeBayar' => 'transfer',
                'dibayar_ke' => 'admin@sans-speed.local',
                'createdAt' => '2026-04-01 08:00:00',
            ]);
            DB::table('pembukuan')->insert([
                'id' => (string) Str::uuid(),
                'tanggal' => '2026-04-25 10:00:00',
                'jenis' => 'pemasukan',
                'kategori' => 'Tagihan Internet',
                'nominal' => $p->totalFinal ?? $p->hargaPaket,
                'keterangan' => 'Pembayaran tagihan ' . $p->nama . ' (' . $p->idPelanggan . ') bulan 4/2026',
                'idReferensi' => $tagId,
                'createdBy' => DB::table('users')->where('role', 'owner')->value('id'),
                'createdAt' => '2026-04-25 10:00:00',
            ]);
        }

        echo "TestDataSeeder complete!\n";
        echo "  Tagihan: " . DB::table('tagihan_bulanan')->count() . "\n";
        echo "  Pembukuan: " . DB::table('pembukuan')->count() . "\n";
    }
}
