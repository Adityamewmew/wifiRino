<?php

declare(strict_types=1);

/**
 * Uji cepat alur API utama (butuh admin di DB + kredensial di bawah):
 * php scripts/smoke_features.php
 */
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);

function dispatch(Kernel $kernel, string $method, string $uri, array $headers = [], ?string $body = null): Response|Symfony\Component\HttpFoundation\Response
{
    $server = ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'];
    foreach ($headers as $k => $v) {
        $server['HTTP_'.strtoupper(str_replace('-', '_', $k))] = $v;
    }

    $req = Request::create($uri, $method, [], [], [], $server, $body);

    return $kernel->handle($req);
}

$fail = 0;
$check = function (string $label, $response, array $expectStatus) use (&$fail) {
    $code = $response->getStatusCode();
    if (! in_array($code, $expectStatus, true)) {
        echo "FAIL {$label}: HTTP {$code} ".$response->getContent()."\n";
        $fail++;
    } else {
        echo "OK   {$label}\n";
    }
};

$login = dispatch($kernel, 'POST', '/api/auth/login', [], json_encode([
    'email' => 'admin@gmail.com',
    'password' => 'SansSpeed2026!',
]));
$check('login', $login, [200]);
$loginJson = json_decode($login->getContent(), true);
$token = $loginJson['token'] ?? '';
if ($token === '') {
    echo "No token, abort\n";
    exit(1);
}
$auth = ['Authorization' => 'Bearer '.$token];

$ts = (string) time();

$r = dispatch($kernel, 'POST', '/api/collections/areas', $auth, json_encode([
    'nama' => 'Smoke Area '.$ts,
    'keterangan' => 'smoke',
]));
$check('POST areas', $r, [200]);

$r = dispatch($kernel, 'POST', '/api/collections/paket', $auth, json_encode([
    'nama' => 'Smoke Paket '.$ts,
    'harga' => 150000,
    'aktif' => 1,
]));
$check('POST paket', $r, [200]);

$r = dispatch($kernel, 'POST', '/api/collections/pelanggan', $auth, json_encode([
    'nama' => 'Smoke Pel '.$ts,
    'idPelanggan' => 'SMOKE-'.$ts,
    'noWA' => '08123456789',
    'area' => 'Smoke Area '.$ts,
    'paket' => 'Smoke Paket '.$ts,
    'hargaPaket' => 150000,
    'status' => 'aktif',
    'tglTagih' => 10,
    'createdAt' => gmdate('Y-m-d\TH:i:s\Z'),
    'updatedAt' => gmdate('Y-m-d\TH:i:s\Z'),
]));
$check('POST pelanggan', $r, [200]);
$pelId = json_decode($r->getContent(), true)['id'] ?? '';

$r = dispatch($kernel, 'POST', '/api/collections/tagihan_bulanan', $auth, json_encode([
    'idPelanggan' => 'SMOKE-'.$ts,
    'namaPelanggan' => 'Smoke Pel '.$ts,
    'area' => 'Smoke Area '.$ts,
    'paket' => 'Smoke Paket '.$ts,
    'bulan' => (int) date('n'),
    'tahun' => (int) date('Y'),
    'totalTagihan' => 150000,
    'status' => 'belum',
    'tglJatuhTempo' => gmdate('Y-m-d\TH:i:s\Z'),
    'createdAt' => gmdate('Y-m-d\TH:i:s\Z'),
]));
$check('POST tagihan_bulanan', $r, [200]);
$tagId = json_decode($r->getContent(), true)['id'] ?? '';

$nm = (int) date('n');
$ny = (int) date('Y');
$nm2 = $nm === 12 ? 1 : $nm + 1;
$ny2 = $nm === 12 ? $ny + 1 : $ny;
$r = dispatch($kernel, 'POST', '/api/collections/tagihan_bulanan', $auth, json_encode([
    'idPelanggan' => 'SMOKE-'.$ts,
    'namaPelanggan' => 'Smoke Pel '.$ts,
    'area' => 'Smoke Area '.$ts,
    'paket' => 'Smoke Paket '.$ts,
    'bulan' => $nm2,
    'tahun' => $ny2,
    'totalTagihan' => 150000,
    'status' => 'belum',
    'tglJatuhTempo' => gmdate('Y-m-d\TH:i:s\Z'),
    'createdAt' => gmdate('Y-m-d\TH:i:s\Z'),
]));
$check('POST tagihan_bulanan bulan2', $r, [200]);

$r = dispatch($kernel, 'POST', '/api/collections/pembukuan', $auth, json_encode([
    'jenis' => 'pemasukan',
    'nominal' => 50000,
    'kategori' => 'Lainnya',
    'keterangan' => 'smoke test',
    'tanggal' => gmdate('Y-m-d\TH:i:s\Z'),
]));
$check('POST pembukuan', $r, [200]);

$r = dispatch($kernel, 'POST', '/api/collections/pengumuman', $auth, json_encode([
    'targetType' => 'global',
    'pesan' => 'Smoke siaran '.$ts,
    'startAt' => gmdate('Y-m-d\TH:i:s\Z'),
    'endAt' => null,
    'aktif' => 1,
]));
$check('POST pengumuman', $r, [200]);

$r = dispatch($kernel, 'POST', '/api/tugas', $auth, json_encode([
    'judul' => 'Smoke tugas '.$ts,
    'deskripsi' => 'test',
    'jenisTask' => 'survey',
    'prioritas' => 'normal',
    'assignTo' => '__all__',
    'assignToNama' => 'Semua Teknisi',
]));
$check('POST tugas', $r, [200]);

$r = dispatch($kernel, 'POST', '/api/mikrotik-routers', $auth, json_encode([
    'nama' => 'Smoke Router '.$ts,
    'host' => '127.0.0.1',
    'apiPort' => 8728,
    'apiUser' => 'admin',
    'apiPassword' => 'secret',
    'rosVersi' => 'V6',
    'hotspotManager' => 'tidak_aktif',
    'serviceType' => 'API',
]));
$check('POST mikrotik-routers', $r, [200]);
$mikId = json_decode($r->getContent(), true)['id'] ?? '';

$r = dispatch($kernel, 'PUT', '/api/pelanggan/'.$pelId.'/mikrotik', $auth, json_encode([
    'routerId' => $mikId,
    'profile' => 'default',
]));
$check('PUT pelanggan mikrotik', $r, [200]);

$r = dispatch($kernel, 'GET', '/api/pelanggan/SMOKE-'.$ts.'/bayar-dimuka/latest', $auth);
$check('GET bayar-dimuka latest', $r, [200]);

$r = dispatch($kernel, 'POST', '/api/pelanggan/SMOKE-'.$ts.'/bayar-dimuka', $auth, json_encode([
    'periodeList' => [['bulan' => $nm2, 'tahun' => $ny2]],
    'keterangan' => 'smoke dimuka',
]));
$check('POST bayar-dimuka', $r, [200]);

$r = dispatch($kernel, 'POST', '/api/tagihan/'.$tagId.'/bayar', $auth);
$check('POST tagihan bayar', $r, [200]);

$r = dispatch($kernel, 'GET', '/api/mikrotik-routers', $auth);
$check('GET mikrotik-routers', $r, [200]);

$r = dispatch($kernel, 'GET', '/api/tugas', $auth);
$check('GET tugas', $r, [200]);

$r = dispatch($kernel, 'GET', '/api/stats/revenue-trend?limit=3', $auth);
$check('GET stats revenue', $r, [200]);

$r = dispatch($kernel, 'GET', '/api/stats/keuangan?bulan='.date('n').'&tahun='.date('Y'), $auth);
$check('GET stats keuangan', $r, [200]);

$r = dispatch($kernel, 'GET', '/api/audit', $auth);
$check('GET audit', $r, [200]);

$r = dispatch($kernel, 'GET', '/api/chat/threads', $auth);
$check('GET chat threads', $r, [200]);

echo $fail === 0 ? "\nAll smoke checks passed.\n" : "\nSmoke failures: {$fail}\n";
exit($fail === 0 ? 0 : 1);
