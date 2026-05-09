@extends('layouts.admin')
@section('title', 'Area / Wilayah - Sans Speed')

@php
    $user = session('staff_user', []);
    $isOwner = ($user['roleKey'] ?? $user['role'] ?? '') === 'owner';
@endphp

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <h1 class="page-title" style="margin: 0; font-size: 24px; font-weight: 700; color: #f8fafc;">
        <i class="fas fa-map-marked-alt" style="color:#6366f1;"></i> Manajemen Area / Wilayah
    </h1>
    <button type="button" onclick="document.getElementById('modalTambahArea').classList.add('active')" class="btn-primary" style="background: #10b981; color: white; padding: 10px 18px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
        <i class="fas fa-plus"></i> Tambah Area
    </button>
</div>

<div class="card-3d" style="border-radius: 12px; padding: 24px;">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align:left; padding:12px 16px; font-size:12px; font-weight:600; color:#6b7280; border-bottom:1px solid #e5e7eb; background:#f8fafc;">No</th>
                    <th style="text-align:left; padding:12px 16px; font-size:12px; font-weight:600; color:#6b7280; border-bottom:1px solid #e5e7eb; background:#f8fafc;">Nama Area</th>
                    <th style="text-align:left; padding:12px 16px; font-size:12px; font-weight:600; color:#6b7280; border-bottom:1px solid #e5e7eb; background:#f8fafc;">Keterangan</th>
                    <th style="text-align:left; padding:12px 16px; font-size:12px; font-weight:600; color:#6b7280; border-bottom:1px solid #e5e7eb; background:#f8fafc;">Dibuat</th>
                    <th style="text-align:right; padding:12px 16px; font-size:12px; font-weight:600; color:#6b7280; border-bottom:1px solid #e5e7eb; background:#f8fafc;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($areas as $i => $a)
                <tr>
                    <td style="padding:12px 16px; font-size:13px; border-bottom:1px solid #f3f4f6; color:#64748b;">{{ $i + 1 }}</td>
                    <td style="padding:12px 16px; font-size:14px; border-bottom:1px solid #f3f4f6; font-weight:700; color:#0f172a;">{{ $a['nama'] ?? '-' }}</td>
                    <td style="padding:12px 16px; font-size:13px; border-bottom:1px solid #f3f4f6; color:#64748b;">{{ $a['keterangan'] ?? '-' }}</td>
                    <td style="padding:12px 16px; font-size:12px; border-bottom:1px solid #f3f4f6; color:#94a3b8;">{{ $a['createdAt'] ?? '-' }}</td>
                    <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6; text-align:right;">
                        <button onclick="editArea('{{ $a['id'] }}', '{{ addslashes($a['nama'] ?? '') }}', '{{ addslashes($a['keterangan'] ?? '') }}')" class="action-btn" style="color:#3b82f6; background:rgba(59,130,246,0.1); border:none; cursor:pointer; padding:6px 10px; border-radius:6px;"><i class="fas fa-edit"></i></button>
                        @if($isOwner)
                        <form method="POST" action="{{ url('/area/' . $a['id']) }}" style="display:inline;" onsubmit="return confirm('Yakin hapus area ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="action-btn" style="color:#ef4444; background:rgba(239,68,68,0.1); border:none; cursor:pointer; padding:6px 10px; border-radius:6px;"><i class="fas fa-trash"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center; color:#94a3b8; padding:30px;">Belum ada area.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:15px; font-size:13px; color:#64748b;">Total: {{ count($areas) }} Area</div>
</div>

<!-- Modal Tambah -->
<div id="modalTambahArea" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2 style="margin:0; font-size:18px; color:#0f172a;"><i class="fas fa-plus-circle" style="color:#10b981;"></i> Tambah Area Baru</h2>
            <button type="button" onclick="document.getElementById('modalTambahArea').classList.remove('active')" style="background:transparent; border:none; font-size:20px; color:#94a3b8; cursor:pointer;"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="{{ url('/area') }}">
            @csrf
            <div class="form-group">
                <label style="display:block; margin-bottom:6px; font-size:13px; font-weight:600; color:#475569;">Nama Area <span style="color:red;">*</span></label>
                <input type="text" name="nama" class="form-input" style="width:100%; box-sizing:border-box; padding:10px; border:1px solid #e2e8f0; border-radius:8px;" required>
            </div>
            <div class="form-group">
                <label style="display:block; margin-bottom:6px; font-size:13px; font-weight:600; color:#475569;">Keterangan</label>
                <textarea name="keterangan" class="form-input" style="width:100%; box-sizing:border-box; padding:10px; border:1px solid #e2e8f0; border-radius:8px; min-height:60px; resize:vertical;"></textarea>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="submit" class="btn-primary" style="background:#10b981; color:white; padding:10px 20px; border:none; border-radius:8px; font-weight:600; cursor:pointer;"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modalEditArea" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2 style="margin:0; font-size:18px; color:#0f172a;"><i class="fas fa-edit" style="color:#3b82f6;"></i> Edit Area</h2>
            <button type="button" onclick="document.getElementById('modalEditArea').classList.remove('active')" style="background:transparent; border:none; font-size:20px; color:#94a3b8; cursor:pointer;"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" id="formEditArea" action="">
            @csrf @method('PUT')
            <div class="form-group">
                <label style="display:block; margin-bottom:6px; font-size:13px; font-weight:600; color:#475569;">Nama Area <span style="color:red;">*</span></label>
                <input type="text" name="nama" id="editAreaNama" class="form-input" style="width:100%; box-sizing:border-box; padding:10px; border:1px solid #e2e8f0; border-radius:8px;" required>
            </div>
            <div class="form-group">
                <label style="display:block; margin-bottom:6px; font-size:13px; font-weight:600; color:#475569;">Keterangan</label>
                <textarea name="keterangan" id="editAreaKet" class="form-input" style="width:100%; box-sizing:border-box; padding:10px; border:1px solid #e2e8f0; border-radius:8px; min-height:60px; resize:vertical;"></textarea>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px;">
                <button type="submit" class="btn-primary" style="background:#3b82f6; color:white; padding:10px 20px; border:none; border-radius:8px; font-weight:600; cursor:pointer;"><i class="fas fa-save"></i> Simpan</button>
            </div>
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
function editArea(id, nama, ket) {
    document.getElementById('formEditArea').action = '/area/' + id;
    document.getElementById('editAreaNama').value = nama;
    document.getElementById('editAreaKet').value = ket;
    document.getElementById('modalEditArea').classList.add('active');
}
</script>
@endpush
