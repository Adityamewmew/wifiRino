@php
    $prefix = $editMode ? 'edit_' : '';
@endphp
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
    <div class="form-group">
        <label style="display:block; margin-bottom:6px; font-size:13px; font-weight:600; color:#475569;">ID Pelanggan <span style="color:#10b981; font-size:11px;">(Opsional)</span></label>
        <input type="text" name="idPelanggan" class="form-input" style="width:100%; box-sizing:border-box; padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px;" placeholder="Kosongkan untuk otomatis">
    </div>
    <div class="form-group">
        <label style="display:block; margin-bottom:6px; font-size:13px; font-weight:600; color:#475569;">Nama Lengkap <span style="color:red;">*</span></label>
        <input type="text" name="nama" class="form-input" style="width:100%; box-sizing:border-box; padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px;" required>
    </div>
    <div class="form-group">
        <label style="display:block; margin-bottom:6px; font-size:13px; font-weight:600; color:#475569;">No. WhatsApp <span style="color:red;">*</span></label>
        <input type="tel" name="noWA" class="form-input" style="width:100%; box-sizing:border-box; padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px;" placeholder="08..." required>
    </div>
    <div class="form-group">
        <label style="display:block; margin-bottom:6px; font-size:13px; font-weight:600; color:#475569;">Paket <span style="color:red;">*</span></label>
        <select name="paket" class="form-select" style="width:100%; box-sizing:border-box; padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px;" required onchange="updateHargaPaket(this)">
            <option value="">-- Pilih Paket --</option>
            @foreach($paketAktif as $pk)
                <option value="{{ $pk['nama'] }}" data-harga="{{ $pk['harga'] ?? 0 }}">{{ $pk['nama'] }} - Rp {{ number_format($pk['harga'] ?? 0, 0, ',', '.') }}</option>
            @endforeach
        </select>
        <input type="hidden" name="hargaPaket" value="0">
    </div>
    <div class="form-group">
        <label style="display:block; margin-bottom:6px; font-size:13px; font-weight:600; color:#475569;">Area <span style="color:red;">*</span></label>
        <select name="area" class="form-select" style="width:100%; box-sizing:border-box; padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px;" required>
            <option value="">-- Pilih Area --</option>
            @foreach($areas as $a)
                <option value="{{ $a }}">{{ $a }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label style="display:block; margin-bottom:6px; font-size:13px; font-weight:600; color:#475569;">Tgl Jatuh Tempo (1-28) <span style="color:red;">*</span></label>
        <input type="number" name="tglTagih" class="form-input" style="width:100%; box-sizing:border-box; padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px;" min="1" max="28" required>
    </div>
    <div class="form-group">
        <label style="display:block; margin-bottom:6px; font-size:13px; font-weight:600; color:#475569;">📅 Mulai Berlangganan <span style="color:red;">*</span></label>
        <input type="date" name="tanggalMulaiStr" class="form-input" style="width:100%; box-sizing:border-box; padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px;" required>
    </div>
    <div class="form-group">
        <label style="display:block; margin-bottom:6px; font-size:13px; font-weight:600; color:#475569;">ID PPOE</label>
        <input type="text" name="idPPOE" class="form-input" style="width:100%; box-sizing:border-box; padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px;" placeholder="Opsional">
    </div>
    <div class="form-group">
        <label style="display:block; margin-bottom:6px; font-size:13px; font-weight:600; color:#475569;">Email</label>
        <input type="email" name="email" class="form-input" style="width:100%; box-sizing:border-box; padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px;">
    </div>
    <div class="form-group">
        <label style="display:block; margin-bottom:6px; font-size:13px; font-weight:600; color:#475569;">No. KTP</label>
        <input type="text" name="noKtp" class="form-input" style="width:100%; box-sizing:border-box; padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px;">
    </div>
</div>

@if($editMode)
<div class="form-group" style="margin-top:10px;">
    <label style="display:block; margin-bottom:6px; font-size:13px; font-weight:600; color:#475569;">Status</label>
    <select name="status" class="form-select" style="width:100%; box-sizing:border-box; padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px;">
        <option value="aktif">Aktif</option>
        <option value="isolir">Isolir</option>
        <option value="berhenti">Berhenti</option>
    </select>
</div>
@else
<input type="hidden" name="status" value="aktif">
@endif

<div class="form-group" style="margin-top:10px;">
    <label style="display:block; margin-bottom:6px; font-size:13px; font-weight:600; color:#475569;">Alamat</label>
    <textarea name="alamat" class="form-input" style="width:100%; box-sizing:border-box; padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px; min-height:60px; resize:vertical;"></textarea>
</div>
<div class="form-group">
    <label style="display:block; margin-bottom:6px; font-size:13px; font-weight:600; color:#475569;">Keterangan</label>
    <textarea name="keterangan" class="form-input" style="width:100%; box-sizing:border-box; padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px; min-height:40px; resize:vertical;"></textarea>
</div>

<!-- Biaya Tambahan -->
<details style="margin-top:10px; border: 1px dashed #cbd5e1; border-radius: 10px; padding: 12px;">
    <summary style="cursor:pointer; font-weight:600; font-size:13px; color:#475569;"><i class="fas fa-plus-circle" style="color:#6366f1;"></i> Biaya Tambahan / Diskon</summary>
    <div style="margin-top:12px; display:grid; grid-template-columns:2fr 1fr; gap:10px;">
        <input type="text" name="biayaTambahan1_rincian" class="form-input" placeholder="Biaya Tambahan 1" style="width:100%; box-sizing:border-box; padding:6px; border:1px solid #e2e8f0; border-radius:6px;">
        <input type="number" name="biayaTambahan1_nominal" class="form-input" placeholder="Rp 0" style="width:100%; box-sizing:border-box; padding:6px; border:1px solid #e2e8f0; border-radius:6px;">
    </div>
    <div style="margin-top:8px; display:grid; grid-template-columns:2fr 1fr; gap:10px;">
        <input type="text" name="biayaTambahan2_rincian" class="form-input" placeholder="Biaya Tambahan 2" style="width:100%; box-sizing:border-box; padding:6px; border:1px solid #e2e8f0; border-radius:6px;">
        <input type="number" name="biayaTambahan2_nominal" class="form-input" placeholder="Rp 0" style="width:100%; box-sizing:border-box; padding:6px; border:1px solid #e2e8f0; border-radius:6px;">
    </div>
    <div style="margin-top:8px; display:grid; grid-template-columns:2fr 1fr; gap:10px; background:#fef2f2; padding:10px; border-radius:8px;">
        <input type="text" name="diskon_keterangan" class="form-input" placeholder="Keterangan Diskon" style="width:100%; box-sizing:border-box; padding:6px; border:1px solid #fca5a5; border-radius:6px; color:#dc2626;">
        <input type="number" name="diskon_nominal" class="form-input" placeholder="Rp 0" style="width:100%; box-sizing:border-box; padding:6px; border:1px solid #fca5a5; border-radius:6px; color:#dc2626;">
    </div>
</details>

<input type="hidden" name="totalFinal" value="0">
<input type="hidden" name="mulaiTagihan" value="">

<div style="display:flex; justify-content:flex-end; gap:10px; margin-top:20px; padding-top:15px; border-top:1px solid #f1f5f9;">
    <button type="submit" class="btn-primary" style="background:#0ea5e9; color:white; padding:10px 20px; border:none; border-radius:8px; font-weight:600; cursor:pointer;">
        <i class="fas fa-save"></i> {{ $editMode ? 'Simpan Perubahan' : 'Tambah Pelanggan' }}
    </button>
</div>

<script>
function updateHargaPaket(select) {
    const opt = select.options[select.selectedIndex];
    const harga = opt ? (opt.dataset.harga || 0) : 0;
    select.closest('form').querySelector('[name="hargaPaket"]').value = harga;
    select.closest('form').querySelector('[name="totalFinal"]').value = harga;
}
</script>
