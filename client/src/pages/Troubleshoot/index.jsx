import { useState, useEffect } from 'react';
import api from '../../api/client';

const STATUS_CONFIG = {
  pending: { label: 'Menunggu', class: 'badge-danger', color: '#DC2626' },
  proses: { label: 'Diproses', class: 'badge-warning', color: '#D97706' },
  selesai: { label: 'Selesai', class: 'badge-success', color: '#059669' },
};

export default function TroubleshootPage() {
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(false);
  const [filter, setFilter] = useState('pending');
  const [showModal, setShowModal] = useState(false);
  const [selected, setSelected] = useState(null);
  const [form, setForm] = useState({ status: 'pending', catatanTeknisi: '' });

  useEffect(() => { fetchData(); }, []);

  const fetchData = async () => {
    setLoading(true);
    try {
      const res = await api.get('/tugas');
      const all = (res.data.data || []).filter(t => t.jenisTask === 'troubleshoot');
      setData(all);
    } catch (err) { console.error(err); }
    finally { setLoading(false); }
  };

  const filtered = data.filter(t => (t.status || 'pending') === filter);

  const openTask = (task) => {
    setSelected(task);
    setForm({ status: task.status || 'pending', catatanTeknisi: task.catatanTeknisi || '' });
    setShowModal(true);
  };

  const handleUpdate = async (e) => {
    e.preventDefault();
    if (!selected) return;
    try {
      await api.put(`/tugas/${selected.id}`, {
        status: form.status,
        catatanTeknisi: form.catatanTeknisi,
        ...(form.status === 'selesai' ? { tglSelesai: new Date().toISOString().slice(0, 19).replace('T', ' ') } : {}),
      });
      setShowModal(false);
      fetchData();
    } catch (err) { console.error(err); }
  };

  const counts = {
    pending: data.filter(t => (t.status || 'pending') === 'pending').length,
    proses: data.filter(t => t.status === 'proses').length,
    selesai: data.filter(t => t.status === 'selesai').length,
  };

  return (
    <>
      <div className="page-header">
        <div className="page-header-left">
          <h1>⚠️ Gangguan Jaringan</h1>
          <p>Daftar troubleshoot dan laporan gangguan</p>
        </div>
      </div>

      <div style={{ display: 'flex', gap: 8, marginBottom: 20 }}>
        {(['pending', 'proses', 'selesai']).map(key => (
          <button
            key={key}
            className={`btn ${filter === key ? 'btn-primary' : 'btn-secondary'} btn-sm`}
            onClick={() => setFilter(key)}
          >
            {STATUS_CONFIG[key].label} ({counts[key]})
          </button>
        ))}
      </div>

      {loading ? (
        <div className="loading-center"><div className="spinner" /></div>
      ) : (
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(340px, 1fr))', gap: 12 }}>
          {filtered.map(task => {
            const st = STATUS_CONFIG[task.status] || STATUS_CONFIG.pending;
            const dateStr = task.createdAt ? new Date(task.createdAt).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' }) : '-';
            return (
              <div className="glass-card" key={task.id} style={{ padding: 16, cursor: 'pointer', borderLeft: `4px solid ${st.color}` }} onClick={() => openTask(task)}>
                <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 8 }}>
                  <h4 style={{ fontSize: 15, fontWeight: 700, margin: 0 }}>{task.judul || 'Tanpa Judul'}</h4>
                  <span className={`badge ${st.class}`}>{st.label}</span>
                </div>
                <div style={{ display: 'grid', gap: 4, fontSize: 12, color: 'var(--text-muted)' }}>
                  <div>👤 {task.namaPelanggan || '-'} ({task.idPelanggan || '-'})</div>
                  <div>📍 {task.alamat || '-'}</div>
                  <div>🔧 {task.assignToNama || 'Belum di-assign'}</div>
                  <div>🕐 {dateStr}</div>
                </div>
              </div>
            );
          })}
          {!filtered.length && (
            <div className="glass-card" style={{ textAlign: 'center', padding: 60, color: 'var(--text-muted)', gridColumn: '1 / -1' }}>
              Tidak ada tugas troubleshoot berstatus "{STATUS_CONFIG[filter]?.label}".
            </div>
          )}
        </div>
      )}

      {showModal && selected && (
        <div className="modal-overlay" onClick={() => setShowModal(false)}>
          <div className="modal-content" onClick={(e) => e.stopPropagation()}>
            <div className="modal-header">
              <h3>{selected.judul || 'Detail Tugas'}</h3>
              <button className="btn btn-ghost btn-sm" onClick={() => setShowModal(false)}>✕</button>
            </div>
            <form onSubmit={handleUpdate}>
              <div className="modal-body">
                <div style={{ background: 'rgba(245,158,11,0.08)', padding: 12, borderRadius: 8, borderLeft: '3px solid #D97706', marginBottom: 16, fontSize: 13 }}>
                  ℹ️ {selected.deskripsi || 'Tidak ada deskripsi'}
                </div>
                <div style={{ fontSize: 13, color: 'var(--text-secondary)', marginBottom: 16 }}>
                  <div>👤 {selected.namaPelanggan || '-'}</div>
                  <div>📍 {selected.alamat || '-'}</div>
                </div>
                <div className="form-group">
                  <label className="form-label">Ubah Status</label>
                  <select className="form-input" value={form.status} onChange={(e) => setForm({ ...form, status: e.target.value })}>
                    <option value="pending">Menunggu Penanganan</option>
                    <option value="proses">Sedang Dikerjakan</option>
                    <option value="selesai">Selesai</option>
                  </select>
                </div>
                <div className="form-group">
                  <label className="form-label">Catatan Teknisi</label>
                  <textarea className="form-input" rows={3} value={form.catatanTeknisi} onChange={(e) => setForm({ ...form, catatanTeknisi: e.target.value })} placeholder="Tindakan yang dilakukan..." style={{ resize: 'vertical' }} />
                </div>
              </div>
              <div className="modal-footer">
                <button type="button" className="btn btn-secondary" onClick={() => setShowModal(false)}>Batal</button>
                <button type="submit" className="btn btn-primary">Simpan Laporan</button>
              </div>
            </form>
          </div>
        </div>
      )}
    </>
  );
}
