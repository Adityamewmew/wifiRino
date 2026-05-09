---
name: sans-billing-laravel-refactor
description: >
  Panduan refactoring project Laravel "sans-billing-api" — perbaikan struktur controller, 
  migrasi database, penghapusan JS page logic ke Blade PHP, dan restrukturisasi views.
  Gunakan skill ini setiap kali ingin memperbaiki, menambah, atau merefactor bagian 
  manapun dari project sans-billing: controller baru, migrasi baru, halaman baru, 
  atau restruktur views. Juga berlaku saat debugging, naming convention, atau 
  memindahkan logika dari JS ke Blade/PHP.
---

# Sans-Billing Laravel Refactor Guide

## Gambaran Project

`sans-billing-api` adalah sistem billing ISP berbasis Laravel. Project ini melayani:
- **Admin/Owner** — kelola pelanggan, tagihan, laporan keuangan
- **Teknisi** — pemasangan, troubleshoot, tugas lapangan
- **Pelanggan** — portal mandiri, cek tagihan, struk

Stack: Laravel (PHP), Blade templating, SQLite (dev), vanilla JS minimal.

---

## 1. Struktur Controller — Standar Wajib

### Struktur Direktori Controller yang Benar

```
app/Http/Controllers/
├── Controller.php                  ← Base controller
├── Api/                            ← Semua endpoint API (JSON response)
│   ├── Auth/
│   │   └── AuthApiController.php
│   ├── Owner/
│   │   ├── OwnerBulkDeleteController.php
│   │   ├── PelangganMasterController.php
│   │   ├── PelangganMikrotikController.php
│   │   ├── PembukuanAgenSummaryController.php
│   │   ├── TagihanActionController.php
│   │   ├── TagihanSyncController.php
│   │   └── UserDirectoryController.php
│   ├── Teknisi/
│   │   └── TugasTeknisiController.php
│   ├── Public/
│   │   ├── PublicApiController.php
│   │   ├── PengumumanPublicController.php
│   │   └── PushRegisterController.php
│   └── Stats/
│       └── StatsController.php
└── Web/                            ← Semua halaman web (Blade response)
    ├── Auth/
    │   └── LoginController.php
    ├── Admin/
    │   ├── DashboardController.php
    │   ├── PelangganController.php
    │   ├── TagihanController.php
    │   ├── BukuKasController.php
    │   ├── PaketController.php
    │   ├── AreaController.php
    │   ├── PengaturanController.php
    │   └── PengumumanController.php
    ├── Teknisi/
    │   ├── DashboardTeknisiController.php
    │   └── TugasTeknisiWebController.php
    └── Pelanggan/
        └── PortalPelangganController.php
```

### Aturan Penamaan Controller

| Konteks | Suffix | Contoh |
|---------|--------|--------|
| API endpoint | `Controller.php` biasa | `PelangganMasterController.php` |
| Web page | `Controller.php` biasa | `DashboardController.php` |
| Aksi tunggal spesifik | Nama aksi + Controller | `TagihanSyncController.php` |

### Aturan Response Controller

```php
// Api/ Controller → SELALU return JSON
return response()->json([
    'success' => true,
    'data'    => $data,
]);

// Web/ Controller → SELALU return view()
return view('billing.pages.dashboard_admin', compact('data'));
```

### Template Controller API

```php
<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NamaController extends Controller
{
    public function index(Request $request)
    {
        // logika
        return response()->json(['success' => true, 'data' => []]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'field' => 'required|string',
        ]);
        // simpan
        return response()->json(['success' => true, 'message' => 'Berhasil disimpan']);
    }
}
```

### Template Controller Web

```php
<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NamaController extends Controller
{
    public function index()
    {
        $data = []; // ambil dari DB/service
        return view('billing.pages.nama_halaman', compact('data'));
    }
}
```

---

## 2. Migrasi Database — Standar Wajib

### Format Nama File Migrasi

```
YYYY_MM_DD_HHMMSS_deskripsi_singkat.php
```

Contoh yang ada: `2026_05_02_120000_create_sans_speed_billing_schema.php`  
→ **Pertahankan satu file schema besar ini** untuk instalasi awal.

### Struktur Migrasi Tambahan (Alter/Add)

Jika perlu menambah kolom atau tabel baru setelah schema awal:

```
database/migrations/
├── 2026_05_02_120000_create_sans_speed_billing_schema.php  ← JANGAN UBAH
├── 2026_05_10_000001_add_kolom_ke_tabel_pelanggan.php      ← tambahan
└── 2026_05_15_000001_create_tabel_baru.php                 ← tabel baru
```

### Template Migrasi Baru

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->string('kolom_baru')->nullable()->after('kolom_lama');
        });
    }

    public function down(): void
    {
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->dropColumn('kolom_baru');
        });
    }
};
```

### Aturan Kolom Wajib

Setiap tabel HARUS punya:
```php
$table->id();
$table->timestamps();  // created_at, updated_at
// Opsional tapi dianjurkan:
$table->softDeletes(); // deleted_at
```

---

## 3. JS Pages — Aturan Penghapusan & Migrasi ke Blade

### Prinsip Utama

> **Tidak ada logic atau tampilan di JS murni.** Semua rendering halaman pakai Blade PHP.  
> JS hanya untuk: AJAX call ke API, interaksi UI ringan (toggle, modal), dan tidak ada state management.

### Pemetaan File JS yang Harus Dihapus

| File JS Lama | Pindah Ke |
|---|---|
| `public/js/pages/dashboard.js` | Logic → `DashboardController.php`, Tampilan → `dashboard_admin.blade.php` |
| `public/js/pages/pelanggan.js` | Logic → `PelangganController.php`, Tampilan → `pelanggan.blade.php` |
| `public/js/pages/messaging-pelanggan.js` | Logic → `MessagingController.php`, Tampilan → `messaging_pelanggan.blade.php` |

### Yang BOLEH Tetap di JS

```
public/js/
├── app.js              ← Bootstrap/init global (boleh, tapi minimal)
├── api-config.js       ← Base URL API, auth header (boleh)
└── utils/              ← Helper murni: format rupiah, tanggal, dll (boleh)
```

### Yang HARUS Dipindah ke Blade/PHP

```javascript
// ❌ JANGAN di JS — ini logic halaman
fetch('/api/pelanggan').then(r => r.json()).then(data => {
    document.getElementById('table').innerHTML = data.map(p => 
        `<tr><td>${p.nama}</td></tr>`
    ).join('');
});

// ✅ LAKUKAN di Blade — render server-side
// PelangganController.php
public function index() {
    $pelanggan = Pelanggan::all();
    return view('billing.pages.pelanggan', compact('pelanggan'));
}
```

```blade
{{-- pelanggan.blade.php --}}
@foreach ($pelanggan as $p)
    <tr><td>{{ $p->nama }}</td></tr>
@endforeach
```

### AJAX yang Masih Boleh di JS

```javascript
// ✅ AJAX ringan untuk aksi (bukan render halaman)
document.getElementById('btn-bayar').addEventListener('click', function() {
    fetch('/api/tagihan/bayar', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
        body: JSON.stringify({ id: tagihanId })
    }).then(r => r.json()).then(res => {
        if (res.success) location.reload(); // atau update elemen kecil
    });
});
```

---

## 4. Struktur Views — Standar Wajib

### Struktur Direktori Views yang Benar

```
resources/views/
├── layouts/
│   ├── app_admin.blade.php         ← Layout utama admin/owner
│   ├── app_teknisi.blade.php       ← Layout teknisi
│   └── app_pelanggan.blade.php     ← Layout portal pelanggan
├── partials/
│   ├── sidebar_admin.blade.php
│   ├── sidebar_teknisi.blade.php
│   ├── navbar.blade.php
│   ├── footer.blade.php
│   └── alerts.blade.php            ← Flash message, error box
├── auth/
│   └── login.blade.php
└── billing/
    ├── admin/                      ← Halaman khusus admin/owner
    │   ├── dashboard.blade.php
    │   ├── pelanggan/
    │   │   ├── index.blade.php
    │   │   ├── create.blade.php
    │   │   └── edit.blade.php
    │   ├── tagihan/
    │   │   ├── index.blade.php
    │   │   └── detail.blade.php
    │   ├── buku_kas.blade.php
    │   ├── paket/
    │   │   └── index.blade.php
    │   ├── area/
    │   │   └── index.blade.php
    │   ├── pengaturan.blade.php
    │   ├── pengumuman.blade.php
    │   └── pembukuan/
    │       ├── kang_tagih.blade.php
    │       └── saya.blade.php
    ├── teknisi/                    ← Halaman khusus teknisi
    │   ├── dashboard.blade.php
    │   ├── tugas/
    │   │   └── index.blade.php
    │   ├── pemasangan_baru.blade.php
    │   ├── troubleshoot.blade.php
    │   └── messaging.blade.php
    └── pelanggan/                  ← Halaman portal pelanggan
        ├── portal.blade.php
        ├── tagihan.blade.php
        ├── struk.blade.php
        └── struk_pembayaran.blade.php
```

### Mapping File Lama → File Baru

| File Lama (jelek) | File Baru (benar) |
|---|---|
| `pages/app_pelanggan.blade.php` | `layouts/app_pelanggan.blade.php` |
| `pages/app_teknisi.blade.php` | `layouts/app_teknisi.blade.php` |
| `pages/dashboard_admin.blade.php` | `billing/admin/dashboard.blade.php` |
| `pages/dashboard_teknisi.blade.php` | `billing/teknisi/dashboard.blade.php` |
| `pages/pelanggan.blade.php` | `billing/admin/pelanggan/index.blade.php` |
| `pages/tagihan.blade.php` | `billing/admin/tagihan/index.blade.php` |
| `pages/tagih_pelanggan.blade.php` | `billing/admin/tagihan/detail.blade.php` |
| `pages/buku_kas.blade.php` | `billing/admin/buku_kas.blade.php` |
| `pages/paket.blade.php` | `billing/admin/paket/index.blade.php` |
| `pages/area.blade.php` | `billing/admin/area/index.blade.php` |
| `pages/pengaturan.blade.php` | `billing/admin/pengaturan.blade.php` |
| `pages/pengumuman.blade.php` | `billing/admin/pengumuman.blade.php` |
| `pages/pembukuan_kang_tagih.blade.php` | `billing/admin/pembukuan/kang_tagih.blade.php` |
| `pages/pembukuan_saya.blade.php` | `billing/admin/pembukuan/saya.blade.php` |
| `pages/tugas_teknisi.blade.php` | `billing/teknisi/tugas/index.blade.php` |
| `pages/pemasangan_baru.blade.php` | `billing/teknisi/pemasangan_baru.blade.php` |
| `pages/troubleshoot.blade.php` | `billing/teknisi/troubleshoot.blade.php` |
| `pages/messaging_lapangan.blade.php` | `billing/teknisi/messaging.blade.php` |
| `pages/messaging_pelanggan.blade.php` | `billing/admin/pelanggan/messaging.blade.php` |
| `pages/portal_pelanggan.blade.php` | `billing/pelanggan/portal.blade.php` |
| `pages/struk.blade.php` | `billing/pelanggan/struk.blade.php` |
| `pages/struk_pembayaran.blade.php` | `billing/pelanggan/struk_pembayaran.blade.php` |

### Template Blade Standar

```blade
{{-- billing/admin/pelanggan/index.blade.php --}}
@extends('layouts.app_admin')

@section('title', 'Data Pelanggan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h4 class="mb-3">Data Pelanggan</h4>

            @include('partials.alerts')

            <div class="card">
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Paket</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pelanggan as $p)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $p->nama }}</td>
                                <td>{{ $p->paket->nama ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $p->aktif ? 'success' : 'danger' }}">
                                        {{ $p->aktif ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.pelanggan.edit', $p->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data pelanggan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

---

## 5. Routes — Standar Wajib

### Struktur File Routes

```
routes/
├── web.php       ← Semua route Web/ (Blade pages)
├── api.php       ← Semua route Api/ (JSON)
└── auth.php      ← Login, logout (opsional)
```

### Konvensi Penamaan Route

```php
// routes/web.php
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('/pelanggan', PelangganController::class);
    Route::resource('/tagihan', TagihanController::class);
    Route::get('/buku-kas', [BukuKasController::class, 'index'])->name('buku_kas');
});

Route::prefix('teknisi')->name('teknisi.')->middleware(['auth', 'role:teknisi'])->group(function () {
    Route::get('/dashboard', [DashboardTeknisiController::class, 'index'])->name('dashboard');
    Route::resource('/tugas', TugasTeknisiWebController::class);
});

Route::prefix('pelanggan')->name('pelanggan.')->middleware(['auth', 'role:pelanggan'])->group(function () {
    Route::get('/portal', [PortalPelangganController::class, 'index'])->name('portal');
});

// routes/api.php
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::apiResource('pelanggan', PelangganMasterController::class);
    Route::post('tagihan/sync', [TagihanSyncController::class, 'sync']);
    Route::post('tagihan/{id}/bayar', [TagihanActionController::class, 'bayar']);
});
```

---

## 6. Checklist Refactoring — Urutan Pengerjaan

Saat melakukan refactoring project ini, ikuti urutan ini:

### Fase 1 — Controller
- [ ] Buat folder `Api/Owner/`, `Api/Teknisi/`, `Api/Public/`, `Api/Stats/`
- [ ] Pindahkan controller API ke subfolder yang sesuai
- [ ] Update namespace di setiap file controller
- [ ] Update `use` statement di `routes/api.php`
- [ ] Buat folder `Web/Admin/`, `Web/Teknisi/`, `Web/Pelanggan/`
- [ ] Pindahkan controller Web ke subfolder yang sesuai
- [ ] Update namespace & routes/web.php

### Fase 2 — Migrasi Database
- [ ] Jangan ubah file migrasi schema utama
- [ ] Buat migrasi baru hanya untuk perubahan tambahan
- [ ] Pastikan setiap migrasi punya method `down()` yang benar
- [ ] Jalankan `php artisan migrate:fresh` di dev untuk validasi

### Fase 3 — Hapus JS Pages
- [ ] Audit `public/js/pages/` — identifikasi logic apa yang ada
- [ ] Pindahkan data fetching ke Controller (server-side render)
- [ ] Pindahkan template HTML ke Blade
- [ ] Sisakan hanya AJAX aksi (POST/PUT/DELETE) dan helper kecil
- [ ] Hapus file JS yang sudah dikosongkan

### Fase 4 — Restruktur Views
- [ ] Buat struktur folder baru sesuai panduan di atas
- [ ] Buat layout files di `resources/views/layouts/`
- [ ] Buat partial files di `resources/views/partials/`
- [ ] Pindahkan dan rename semua blade files sesuai mapping tabel
- [ ] Update semua `return view('...')` di controller
- [ ] Update semua `@extends('...')` dan `@include('...')` di blade files
- [ ] Hapus folder `pages/` lama setelah semua dipindah

### Fase 5 — Validasi
- [ ] Jalankan semua route dengan `php artisan route:list`
- [ ] Test tiap halaman: admin, teknisi, pelanggan
- [ ] Test semua endpoint API
- [ ] Pastikan tidak ada `view not found` error

---

## 7. Aturan Umum Tambahan

- **Tidak ada inline PHP logic di Blade** — semua query/logika di Controller atau Model
- **Gunakan `compact()`** untuk passing data ke view
- **Gunakan `@forelse`** bukan `@foreach` + `@if count > 0`
- **Gunakan named routes** — jangan hardcode URL di Blade
- **Selalu ada `@csrf`** di setiap form
- **Validation di Controller** — jangan di Blade atau JS
- **Flash message** lewat `session()->flash()` bukan JS alert

---

## 8. Contoh Implementasi Lengkap (End-to-End)

### Tambah Halaman Baru: "Paket Internet"

**1. Migration (jika tabel belum ada)**
```bash
php artisan make:migration create_paket_table
```

**2. Controller**
```
app/Http/Controllers/Web/Admin/PaketController.php
```

**3. View**
```
resources/views/billing/admin/paket/index.blade.php
resources/views/billing/admin/paket/create.blade.php
resources/views/billing/admin/paket/edit.blade.php
```

**4. Route**
```php
// routes/web.php
Route::resource('admin/paket', PaketController::class)->names('admin.paket');
```

**5. Tidak perlu file JS baru** — semua render di Blade.

---

*Skill ini adalah living document — update saat ada perubahan arsitektur project.*
