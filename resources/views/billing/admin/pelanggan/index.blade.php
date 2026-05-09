@extends('layouts.admin')
@section('title', 'Master Data Pelanggan - Sans Speed')

@php
    $formatRp = fn($n) => 'Rp ' . number_format((float)$n, 0, ',', '.');
    $user = session('staff_user', []);
    $isOwner = ($user['roleKey'] ?? $user['role'] ?? '') === 'owner';
@endphp

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <h1 class="page-title" style="margin: 0; font-size: 24px; font-weight: 700; color: #f8fafc;">
        <i class="fas fa-users-cog" style="color:#f97316;"></i> Master Data Pelanggan
    </h1>
</div>

<!-- Summary Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 18px; margin-bottom: 24px;">
    <div class="card-3d" style="padding: 20px 18px; border-radius: 16px;">
        <div style="width:40px; height:40px; border-radius:12px; background: linear-gradient(135deg, #10b981, #059669); display:flex; align-items:center; justify-content:center; margin-bottom:12px; box-shadow: 0 4px 12px rgba(16,185,129,0.4);"><i class="fas fa-users" style="color:white;"></i></div>
        <div style="font-size: 11px; color: var(--text-secondary,#64748b); font-weight: 700; text-transform: uppercase;">Total Pelanggan</div>
        <div style="font-size: 30px; font-weight: 900; background: linear-gradient(135deg,#10b981,#3b82f6); -webkit-background-clip:text; background-clip:text; -webkit-text-fill-color:transparent;">{{ $sTotal }}</div>
    </div>
    <div class="card-3d" style="padding: 20px 18px; border-radius: 16px;">
        <div style="width:40px; height:40px; border-radius:12px; background: linear-gradient(135deg, #3b82f6, #2563eb); display:flex; align-items:center; justify-content:center; margin-bottom:12px; box-shadow: 0 4px 12px rgba(59,130,246,0.4);"><i class="fas fa-wifi" style="color:white;"></i></div>
        <div style="font-size: 11px; color: var(--text-secondary,#64748b); font-weight: 700; text-transform: uppercase;">Aktif</div>
        <div style="font-size: 30px; font-weight: 900; color: #3b82f6;">{{ $sAktif }}</div>
    </div>
    <div class="card-3d" style="padding: 20px 18px; border-radius: 16px;">
        <div style="width:40px; height:40px; border-radius:12px; background: linear-gradient(135deg, #f59e0b, #d97706); display:flex; align-items:center; justify-content:center; margin-bottom:12px; box-shadow: 0 4px 12px rgba(245,158,11,0.4);"><i class="fas fa-pause-circle" style="color:white;"></i></div>
        <div style="font-size: 11px; color: var(--text-secondary,#64748b); font-weight: 700; text-transform: uppercase;">Isolir</div>
        <div style="font-size: 30px; font-weight: 900; color: #f59e0b;">{{ $sIsolir }}</div>
    </div>
    <div class="card-3d" style="padding: 20px 18px; border-radius: 16px;">
        <div style="width:40px; height:40px; border-radius:12px; background: linear-gradient(135deg, #ef4444, #dc2626); display:flex; align-items:center; justify-content:center; margin-bottom:12px; box-shadow: 0 4px 12px rgba(239,68,68,0.4);"><i class="fas fa-ban" style="color:white;"></i></div>
        <div style="font-size: 11px; color: var(--text-secondary,#64748b); font-weight: 700; text-transform: uppercase;">Berhenti</div>
        <div style="font-size: 30px; font-weight: 900; color: #ef4444;">{{ $sBerhenti }}</div>
    </div>
</div>

<!-- Filter + Actions -->
<div class="card-3d" style="border-radius: 12px; padding: 24px; margin-bottom: 24px;">
    <form method="GET" action="{{ url('/pelanggan') }}" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center; margin-bottom: 15px;">
        <select name="area" class="form-select" style="min-width: 150px; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
            <option value="all">🌐 Semua Area</option>
            @foreach($areas as $a)
                <option value="{{ $a }}" {{ $fArea == $a ? 'selected' : '' }}>{{ $a }}</option>
            @endforeach
        </select>
        <select name="status" class="form-select" style="min-width: 130px; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
            <option value="all" {{ $fStatus == 'all' ? 'selected' : '' }}>Semua Status</option>
            <option value="aktif" {{ $fStatus == 'aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="isolir" {{ $fStatus == 'isolir' ? 'selected' : '' }}>Isolir</option>
            <option value="berhenti" {{ $fStatus == 'berhenti' ? 'selected' : '' }}>Berhenti</option>
        </select>
        <select name="paket" class="form-select" style="min-width: 150px; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
            <option value="all">Semua Paket</option>
            @foreach($paketAll as $p)
                <option value="{{ $p['nama'] ?? '' }}" {{ $fPaket == ($p['nama'] ?? '') ? 'selected' : '' }}>{{ $p['nama'] ?? '' }}</option>
            @endforeach
        </select>
        <input type="text" name="q" value="{{ $fSearch }}" class="form-input" placeholder="Cari nama, ID, WA..." style="width: 220px; padding: 8px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
        <button type="submit" class="btn-primary" style="background: #3b82f6; padding: 8px 14px; color: white; border: none; border-radius: 8px; cursor: pointer;">
            <i class="fas fa-search"></i> Cari
        </button>
        <a href="{{ url('/pelanggan') }}" style="padding: 8px 14px; color: #94a3b8; font-size: 13px; text-decoration: none;">Reset</a>
    </form>

    <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 15px;">
        <button type="button" onclick="document.getElementById('modalTambah').classList.add('active')" class="btn-primary" style="background: #10b981; color: white; padding: 8px 14px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
            <i class="fas fa-user-plus"></i> Pelanggan Baru
        </button>
    </div>

    <!-- Table -->
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left; padding: 12px 16px; font-size: 12px; font-weight: 600; color: #6b7280; border-bottom: 1px solid #e5e7eb; background: #f8fafc;">ID / PPOE</th>
                    <th style="text-align: left; padding: 12px 16px; font-size: 12px; font-weight: 600; color: #6b7280; border-bottom: 1px solid #e5e7eb; background: #f8fafc;">Nama & Kontak</th>
                    <th style="text-align: left; padding: 12px 16px; font-size: 12px; font-weight: 600; color: #6b7280; border-bottom: 1px solid #e5e7eb; background: #f8fafc;">Area</th>
                    <th style="text-align: left; padding: 12px 16px; font-size: 12px; font-weight: 600; color: #6b7280; border-bottom: 1px solid #e5e7eb; background: #f8fafc;">Paket</th>
                    <th style="text-align: left; padding: 12px 16px; font-size: 12px; font-weight: 600; color: #6b7280; border-bottom: 1px solid #e5e7eb; background: #f8fafc;">Tagihan</th>
                    <th style="text-align: left; padding: 12px 16px; font-size: 12px; font-weight: 600; color: #6b7280; border-bottom: 1px solid #e5e7eb; background: #f8fafc;">Status</th>
                    <th style="text-align: right; padding: 12px 16px; font-size: 12px; font-weight: 600; color: #6b7280; border-bottom: 1px solid #e5e7eb; background: #f8fafc;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pelanggan as $p)
                <tr>
                    <td style="padding: 12px 16px; font-size: 13px; border-bottom: 1px solid #f3f4f6;">
                        <div style="font-weight: 700; color: #0f172a;">{{ $p['idPelanggan'] ?? '-' }}</div>
                        @if(!empty($p['idPPOE']))
                        <div style="font-size: 11px; color: #94a3b8;">{{ $p['idPPOE'] }}</div>
                        @endif
                    </td>
                    <td style="padding: 12px 16px; font-size: 13px; border-bottom: 1px solid #f3f4f6;">
                        <div style="font-weight: 600; color: #0f172a;">{{ $p['nama'] ?? '-' }}</div>
                        <div style="font-size: 11px; color: #64748b;">{{ $p['noWA'] ?? '' }}</div>
                    </td>
                    <td style="padding: 12px 16px; font-size: 13px; border-bottom: 1px solid #f3f4f6; color: #475569;">{{ $p['area'] ?? '-' }}</td>
                    <td style="padding: 12px 16px; font-size: 13px; border-bottom: 1px solid #f3f4f6;">
                        <div style="color: #0f172a;">{{ $p['paket'] ?? '-' }}</div>
                        <div style="font-size: 11px; color: #64748b;">Tgl tagih: {{ $p['tglTagih'] ?? '-' }}</div>
                    </td>
                    <td style="padding: 12px 16px; font-size: 13px; border-bottom: 1px solid #f3f4f6; font-weight: 700; color: #0369a1;">{{ $formatRp($p['totalFinal'] ?? $p['hargaPaket'] ?? 0) }}</td>
                    <td style="padding: 12px 16px; font-size: 13px; border-bottom: 1px solid #f3f4f6;">
                        @php $st = $p['status'] ?? 'aktif'; @endphp
                        <span class="badge {{ $st === 'aktif' ? 'badge-success' : ($st === 'isolir' ? 'badge-warning' : 'badge-danger') }}">{{ ucfirst($st) }}</span>
                    </td>
                    <td style="padding: 12px 16px; font-size: 13px; border-bottom: 1px solid #f3f4f6; text-align: right;">
                        <button onclick="editPelanggan({{ json_encode($p, JSON_HEX_APOS|JSON_HEX_QUOT) }})" class="action-btn" title="Edit" style="color: #3b82f6; background: rgba(59,130,246,0.1); border: none; cursor: pointer; padding: 6px 10px; border-radius: 6px;">
                            <i class="fas fa-edit"></i>
                        </button>
                        @if($isOwner)
                        <form method="POST" action="{{ url('/pelanggan/' . $p['id']) }}" style="display:inline;" onsubmit="return confirm('Yakin hapus pelanggan ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="action-btn" title="Hapus" style="color: #ef4444; background: rgba(239,68,68,0.1); border: none; cursor: pointer; padding: 6px 10px; border-radius: 6px;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #94a3b8; padding: 30px;">Tidak ada data pelanggan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top: 15px; font-size: 13px; color: #64748b; text-align: right;">
        Total: {{ count($pelanggan) }} Pelanggan Ditemukan
    </div>
</div>

<!-- MODAL TAMBAH PELANGGAN -->
<div id="modalTambah" class="modal">
    <div class="modal-content" style="max-width: 700px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; border-bottom: 2px solid #f1f5f9; padding-bottom:10px;">
            <h2 style="margin:0; font-size:20px; color:#0f172a;"><i class="fas fa-user-plus"></i> Tambah Pelanggan Baru</h2>
            <button type="button" onclick="document.getElementById('modalTambah').classList.remove('active')" style="background:transparent; border:none; font-size:20px; color:#94a3b8; cursor:pointer;"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="{{ url('/pelanggan') }}">
            @csrf
            @include('admin.pelanggan._form', ['editMode' => false])
        </form>
    </div>
</div>

<!-- MODAL EDIT PELANGGAN -->
<div id="modalEdit" class="modal">
    <div class="modal-content" style="max-width: 700px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; border-bottom: 2px solid #f1f5f9; padding-bottom:10px;">
            <h2 style="margin:0; font-size:20px; color:#0f172a;"><i class="fas fa-user-edit"></i> Edit Pelanggan</h2>
            <button type="button" onclick="document.getElementById('modalEdit').classList.remove('active')" style="background:transparent; border:none; font-size:20px; color:#94a3b8; cursor:pointer;"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" id="formEditPelanggan" action="">
            @csrf @method('PUT')
            @include('admin.pelanggan._form', ['editMode' => true])
        </form>
    </div>
</div>

<style>
    .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15,23,42,0.6); z-index: 1000; justify-content: center; align-items: center; padding: 20px; box-sizing: border-box; }
    .modal.active { display: flex; }
    .modal-content { background: white; border-radius: 16px; padding: 30px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); max-height: 90vh; overflow-y: auto; }
</style>
@endsection

@push('scripts')
<script>
function editPelanggan(p) {
    const form = document.getElementById('formEditPelanggan');
    form.action = '/pelanggan/' + p.id;

    // Populate fields
    const fields = ['idPelanggan','nama','noWA','area','paket','tglTagih','alamat','status','idPPOE',
                     'email','noKtp','tanggalMulaiStr','mulaiTagihan','keterangan','hargaPaket','totalFinal'];
    fields.forEach(f => {
        const el = form.querySelector('[name="'+f+'"]');
        if (el) el.value = p[f] ?? '';
    });

    // Biaya tambahan
    const b1 = p.biayaTambahan1 || {};
    form.querySelector('[name="biayaTambahan1_rincian"]').value = b1.rincian ?? '';
    form.querySelector('[name="biayaTambahan1_nominal"]').value = b1.nominal ?? '';
    const b2 = p.biayaTambahan2 || {};
    form.querySelector('[name="biayaTambahan2_rincian"]').value = b2.rincian ?? '';
    form.querySelector('[name="biayaTambahan2_nominal"]').value = b2.nominal ?? '';
    const dk = p.diskon || {};
    form.querySelector('[name="diskon_keterangan"]').value = dk.keterangan ?? '';
    form.querySelector('[name="diskon_nominal"]').value = dk.nominal ?? '';

    document.getElementById('modalEdit').classList.add('active');
}
</script>
@endpush
