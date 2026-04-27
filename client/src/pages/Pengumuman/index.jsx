import { useState, useEffect } from 'react';
import api from '../../api/client';

export default function PengumumanPage() {
  const [form, setForm] = useState({ targetType: 'global', targetAreaId: '', pesan: '', startAt: '', endAt: '' });
  const [areas, setAreas] = useState([]);
  const [history, setHistory] = useState([]);
  const [loading, setLoading] = useState(false);
  const [sending, setSending] = useState(false);

  useEffect(() => {
    loadAreas();
    loadHistory();
  }, []);

  const loadAreas = async () => {
    try {
      const res = await api.get('/collections/areas');
      setAreas(res.data.data || res.data || []);
    } catch (err) { console.error(err); }
  };

  const loadHistory = async () => {
    setLoading(true);
    try {
      const res = await api.get('/collections/pengumuman?aktif=1');
      const list = (res.data.data || res.data || []).sort((a, b) => new Date(b.createdAt || 0) - new Date(a.createdAt || 0));
      setHistory(list);
    } catch (err) { console.error(err); }
    finally { setLoading(false); }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!form.pesan.trim()) return;
    setSending(true);
    try {
      const payload = {
        targetType: form.targetType,
        targetAreaId: form.targetAreaId || null,
        pesan: form.pesan.trim(),
        startAt: form.startAt || null,
        endAt: form.endAt || null,
        aktif: 1,
      };
      await api.post('/collections/pengumuman', payload);
      setForm({ targetType: 'global', targetAreaId: '', pesan: '', startAt: '', endAt: '' });
      loadHistory();
    } catch (err) { console.error(err); }
    finally { setSending(false); }
  };

  const handleDelete = async (id) => {
    if (!confirm('Cabut siaran ini?')) return;
    try { await api.delete(`/collections/pengumuman/${id}`); loadHistory(); } catch (err) { console.error(err); }
  };

  const formatDate = (iso) => iso ? new Date(iso).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '-';

  return (
    <>
      <div className="page-header">
        <div className="page-header-left">
          <h1>Siaran & Pengumuman</h1>
          <p>Kirim pengumuman ke portal pelanggan</p>
        </div>
      </div>

      <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 20 }}>
        {/* FORM */}
        <div className="glass-card">
          <h3 style={{ margin: '0 0 16px', fontSize: 16 }}>📢 Buat Pengumuman Baru</h3>
          <form onSubmit={handleSubmit}>
            <div className="form-group">
              <label className="form-label">Target Siaran</label>
              <select className="form-input" value={form.targetType} onChange={(e) => setForm({ ...form, targetType: e.target.value })}>
                <option value="global">🌐 Semua Pelanggan (Global)</option>
                <option value="area">📍 Per Area/Wilayah</option>
              </select>
            </div>
            {form.targetType === 'area' && (
              <div className="form-group">
                <label className="form-label">Pilih Area</label>
                <select className="form-input" value={form.targetAreaId} onChange={(e) => setForm({ ...form, targetAreaId: e.target.value })}>
                  <option value="">Pilih area...</option>
                  {areas.map(a => <option key={a.id} value={a.id}>📍 {a.nama}</option>)}
                </select>
              </div>
            )}
            <div className="form-group">
              <label className="form-label">Isi Pesan Siaran</label>
              <textarea className="form-input" rows={4} placeholder="Contoh: Yth pelanggan, akan ada perbaikan fiber optik..." value={form.pesan} onChange={(e) => setForm({ ...form, pesan: e.target.value })} required style={{ resize: 'vertical' }} />
            </div>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12 }}>
              <div className="form-group">
                <label className="form-label">Mulai Tayang</label>
                <input type="datetime-local" className="form-input" value={form.startAt} onChange={(e) => setForm({ ...form, startAt: e.target.value })} />
              </div>
              <div className="form-group">
                <label className="form-label">Berakhir</label>
                <input type="datetime-local" className="form-input" value={form.endAt} onChange={(e) => setForm({ ...form, endAt: e.target.value })} />
              </div>
            </div>

            {/* Preview */}
            <div style={{ background: 'var(--color-primary)', borderRadius: 12, padding: 12, marginBottom: 16 }}>
              <div style={{ fontSize: 11, color: 'rgba(255,255,255,0.6)', marginBottom: 4 }}>PREVIEW PELANGGAN</div>
              <div style={{ fontSize: 13, color: 'white', fontWeight: 500 }}>
                ℹ️ {form.pesan || 'Akan tampil berjalan seperti ini di portal pelanggan...'}
              </div>
            </div>

            <button type="submit" className="btn btn-primary" style={{ width: '100%' }} disabled={sending}>
              {sending ? 'Mengirim...' : '📡 Publikasikan Sekarang'}
            </button>
          </form>
        </div>

        {/* HISTORY */}
        <div className="glass-card">
          <h3 style={{ margin: '0 0 16px', fontSize: 16 }}>📋 Riwayat Siaran Aktif</h3>
          {loading ? (
            <div className="loading-center"><div className="spinner" /></div>
          ) : history.length === 0 ? (
            <div style={{ textAlign: 'center', padding: 40, color: 'var(--text-muted)' }}>Belum ada siaran aktif.</div>
          ) : (
            <div style={{ display: 'grid', gap: 12, maxHeight: 500, overflowY: 'auto' }}>
              {history.map(item => (
                <div key={item.id} style={{ background: 'rgba(0,0,0,0.03)', border: '1px solid rgba(0,0,0,0.06)', borderRadius: 12, padding: 16 }}>
                  <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 8 }}>
                    <span className={`badge badge-${item.targetType === 'global' ? 'info' : 'warning'}`}>
                      {item.targetType === 'global' ? '🌐 Global' : `📍 Area`}
                    </span>
                    <span style={{ fontSize: 11, color: 'var(--text-muted)' }}>{formatDate(item.createdAt)}</span>
                  </div>
                  <p style={{ fontSize: 14, margin: '0 0 8px', lineHeight: 1.5 }}>"{item.pesan || '-'}"</p>
                  {(item.startAt || item.endAt) && (
                    <div style={{ fontSize: 11, color: 'var(--text-muted)', marginBottom: 8 }}>
                      🗓️ {item.startAt ? formatDate(item.startAt) : 'langsung'} {item.endAt ? `s/d ${formatDate(item.endAt)}` : ''}
                    </div>
                  )}
                  <div style={{ textAlign: 'right' }}>
                    <button className="btn btn-ghost btn-sm" onClick={() => handleDelete(item.id)} style={{ color: 'var(--color-danger)' }}>🗑️ Cabut Siaran</button>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      </div>
    </>
  );
}
