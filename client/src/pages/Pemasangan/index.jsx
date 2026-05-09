import { useState } from 'react';
import api from '../../api/client';

export default function PemasanganPage() {
  const [form, setForm] = useState({
    nama: '', noWA: '', alamat: '', area: '', paket: '', idPPOE: '', keterangan: '',
  });
  const [loading, setLoading] = useState(false);
  const [success, setSuccess] = useState('');
  const [error, setError] = useState('');

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError('');
    setSuccess('');
    try {
      // Create pelanggan with status aktif
      await api.post('/pelanggan', {
        ...form,
        status: 'aktif',
        tanggalMulaiStr: new Date().toISOString(),
      });
      setSuccess('Pelanggan baru berhasil didaftarkan!');
      setForm({ nama: '', noWA: '', alamat: '', area: '', paket: '', idPPOE: '', keterangan: '' });
    } catch (err) {
      setError(err.response?.data?.message || 'Gagal mendaftarkan pelanggan');
    } finally {
      setLoading(false);
    }
  };

  return (
    <>
      <div className="page-header">
        <div className="page-header-left">
          <h1>Pemasangan Baru</h1>
          <p>Daftarkan pelanggan baru untuk pemasangan</p>
        </div>
      </div>

      <div className="glass-card" style={{ maxWidth: 640 }}>
        <form onSubmit={handleSubmit}>
          <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16 }}>
            <div className="form-group" style={{ gridColumn: '1 / -1' }}>
              <label className="form-label">Nama Lengkap</label>
              <input className="form-input" placeholder="Nama pelanggan" value={form.nama} onChange={(e) => setForm({ ...form, nama: e.target.value })} required />
            </div>
            <div className="form-group">
              <label className="form-label">No. WhatsApp</label>
              <input className="form-input" placeholder="08xx-xxxx-xxxx" value={form.noWA} onChange={(e) => setForm({ ...form, noWA: e.target.value })} />
            </div>
            <div className="form-group">
              <label className="form-label">Area</label>
              <input className="form-input" placeholder="Nama area" value={form.area} onChange={(e) => setForm({ ...form, area: e.target.value })} />
            </div>
            <div className="form-group" style={{ gridColumn: '1 / -1' }}>
              <label className="form-label">Alamat</label>
              <input className="form-input" placeholder="Alamat lengkap pelanggan" value={form.alamat} onChange={(e) => setForm({ ...form, alamat: e.target.value })} />
            </div>
            <div className="form-group">
              <label className="form-label">Paket Internet</label>
              <input className="form-input" placeholder="Nama paket" value={form.paket} onChange={(e) => setForm({ ...form, paket: e.target.value })} />
            </div>
            <div className="form-group">
              <label className="form-label">ID PPPoE</label>
              <input className="form-input" placeholder="ID PPPoE (opsional)" value={form.idPPOE} onChange={(e) => setForm({ ...form, idPPOE: e.target.value })} />
            </div>
            <div className="form-group" style={{ gridColumn: '1 / -1' }}>
              <label className="form-label">Keterangan</label>
              <input className="form-input" placeholder="Catatan tambahan..." value={form.keterangan} onChange={(e) => setForm({ ...form, keterangan: e.target.value })} />
            </div>
          </div>

          {error && <div style={{ marginTop: 12, padding: '10px 14px', borderRadius: 8, background: 'var(--color-danger-bg)', color: 'var(--color-danger)', fontSize: 13, fontWeight: 500 }}>{error}</div>}
          {success && <div style={{ marginTop: 12, padding: '10px 14px', borderRadius: 8, background: 'var(--color-success-bg)', color: 'var(--color-success)', fontSize: 13, fontWeight: 500 }}>{success}</div>}

          <div style={{ marginTop: 20, display: 'flex', justifyContent: 'flex-end' }}>
            <button type="submit" className="btn btn-primary btn-lg" disabled={loading}>
              {loading ? 'Memproses...' : '🔌 Daftarkan Pemasangan'}
            </button>
          </div>
        </form>
      </div>
    </>
  );
}
