import { useState, useEffect } from 'react';
import api from '../../api/client';

const STATUS_CONFIG = {
  pending: { label: 'Pending', class: 'badge-warning', icon: '⏳' },
  'in-progress': { label: 'Dikerjakan', class: 'badge-info', icon: '🔧' },
  selesai: { label: 'Selesai', class: 'badge-success', icon: '✅' },
  dibatalkan: { label: 'Dibatalkan', class: 'badge-danger', icon: '❌' },
};

const PRIORITAS_CONFIG = {
  normal: { label: 'Normal', class: 'badge-info' },
  tinggi: { label: 'Tinggi', class: 'badge-warning' },
  urgent: { label: 'Urgent', class: 'badge-danger' },
};

export default function TugasTeknisiPage() {
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(false);
  const [showModal, setShowModal] = useState(false);
  const [filter, setFilter] = useState('');
  const [form, setForm] = useState({ judul: '', deskripsi: '', jenisTask: '', prioritas: 'normal', namaPelanggan: '', alamat: '', noWA: '' });

  useEffect(() => { fetchData(); }, [filter]);

  const fetchData = async () => {
    setLoading(true);
    try {
      const params = filter ? { status: filter } : {};
      const res = await api.get('/tugas', { params });
      setData(res.data.data || []);
    } catch (err) { console.error(err); }
    finally { setLoading(false); }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      await api.post('/tugas', form);
      setShowModal(false);
      setForm({ judul: '', deskripsi: '', jenisTask: '', prioritas: 'normal', namaPelanggan: '', alamat: '', noWA: '' });
      fetchData();
    } catch (err) { console.error(err); }
  };

  const handleDelete = async (id) => {
    if (!confirm('Hapus tugas ini?')) return;
    try { await api.delete(`/tugas/${id}`); fetchData(); } catch (err) { console.error(err); }
  };

  const counts = {
    all: data.length,
    pending: data.filter(d => d.status === 'pending').length,
    'in-progress': data.filter(d => d.status === 'in-progress').length,
    selesai: data.filter(d => d.status === 'selesai').length,
  };

  return (
    <>
      <div className="page-header">
        <div className="page-header-left">
          <h1>Tugas Teknisi</h1>
          <p>{counts.all} tugas total — {counts.pending} pending</p>
        </div>
        <button className="btn btn-primary" onClick={() => setShowModal(true)} id="add-tugas-btn">
          + Buat Tugas
        </button>
      </div>

      {/* Filter tabs */}
      <div style={{ display: 'flex', gap: 8, marginBottom: 20 }}>
        {[
          { key: '', label: `Semua (${counts.all})` },
          { key: 'pending', label: `Pending (${counts.pending})` },
          { key: 'in-progress', label: `Dikerjakan (${counts['in-progress']})` },
          { key: 'selesai', label: `Selesai (${counts.selesai})` },
        ].map(tab => (
          <button
            key={tab.key}
            className={`btn ${filter === tab.key ? 'btn-primary' : 'btn-secondary'} btn-sm`}
            onClick={() => setFilter(tab.key)}
          >
            {tab.label}
          </button>
        ))}
      </div>

      {loading ? (
        <div className="loading-center"><div className="spinner" /></div>
      ) : (
        <div style={{ display: 'grid', gap: 12 }}>
          {data.map(task => {
            const st = STATUS_CONFIG[task.status] || STATUS_CONFIG.pending;
            const pr = PRIORITAS_CONFIG[task.prioritas] || PRIORITAS_CONFIG.normal;
            return (
              <div className="glass-card" key={task.id} style={{ padding: 20 }}>
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', gap: 12 }}>
                  <div style={{ flex: 1 }}>
                    <div style={{ display: 'flex', gap: 8, alignItems: 'center', marginBottom: 8 }}>
                      <span style={{ fontSize: 18 }}>{st.icon}</span>
                      <h3 style={{ fontSize: 15, fontWeight: 600 }}>{task.judul || 'Tanpa judul'}</h3>
                      <span className={`badge ${pr.class}`}>{pr.label}</span>
                      <span className={`badge ${st.class}`}>{st.label}</span>
                    </div>
                    {task.deskripsi && <p style={{ fontSize: 13, color: 'var(--text-secondary)', marginBottom: 8 }}>{task.deskripsi}</p>}
                    <div style={{ display: 'flex', gap: 16, fontSize: 12, color: 'var(--text-muted)' }}>
                      {task.namaPelanggan && <span>👤 {task.namaPelanggan}</span>}
                      {task.alamat && <span>📍 {task.alamat}</span>}
                      {task.jenisTask && <span>🏷️ {task.jenisTask}</span>}
                      {task.assignToNama && <span>🔧 {task.assignToNama}</span>}
                    </div>
                  </div>
                  <button className="btn btn-ghost btn-sm" onClick={() => handleDelete(task.id)} title="Hapus">🗑️</button>
                </div>
              </div>
            );
          })}
          {!data.length && (
            <div className="glass-card" style={{ textAlign: 'center', padding: 60, color: 'var(--text-muted)' }}>
              Tidak ada tugas {filter ? `berstatus "${filter}"` : ''}.
            </div>
          )}
        </div>
      )}

      {/* Modal */}
      {showModal && (
        <div className="modal-overlay" onClick={() => setShowModal(false)}>
          <div className="modal-content" onClick={(e) => e.stopPropagation()}>
            <div className="modal-header">
              <h3>Buat Tugas Baru</h3>
              <button className="btn btn-ghost btn-sm" onClick={() => setShowModal(false)}>✕</button>
            </div>
            <form onSubmit={handleSubmit}>
              <div className="modal-body">
                <div className="form-group">
                  <label className="form-label">Judul Tugas</label>
                  <input className="form-input" placeholder="Judul tugas..." value={form.judul} onChange={(e) => setForm({ ...form, judul: e.target.value })} required />
                </div>
                <div className="form-group">
                  <label className="form-label">Deskripsi</label>
                  <input className="form-input" placeholder="Detail tugas..." value={form.deskripsi} onChange={(e) => setForm({ ...form, deskripsi: e.target.value })} />
                </div>
                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12 }}>
                  <div className="form-group">
                    <label className="form-label">Jenis</label>
                    <select className="form-input" value={form.jenisTask} onChange={(e) => setForm({ ...form, jenisTask: e.target.value })}>
                      <option value="">Pilih...</option>
                      <option value="pemasangan">Pemasangan</option>
                      <option value="perbaikan">Perbaikan</option>
                      <option value="maintenance">Maintenance</option>
                      <option value="lainnya">Lainnya</option>
                    </select>
                  </div>
                  <div className="form-group">
                    <label className="form-label">Prioritas</label>
                    <select className="form-input" value={form.prioritas} onChange={(e) => setForm({ ...form, prioritas: e.target.value })}>
                      <option value="normal">Normal</option>
                      <option value="tinggi">Tinggi</option>
                      <option value="urgent">Urgent</option>
                    </select>
                  </div>
                </div>
                <div className="form-group">
                  <label className="form-label">Nama Pelanggan (opsional)</label>
                  <input className="form-input" placeholder="Nama pelanggan..." value={form.namaPelanggan} onChange={(e) => setForm({ ...form, namaPelanggan: e.target.value })} />
                </div>
                <div className="form-group">
                  <label className="form-label">Alamat</label>
                  <input className="form-input" placeholder="Alamat lokasi..." value={form.alamat} onChange={(e) => setForm({ ...form, alamat: e.target.value })} />
                </div>
              </div>
              <div className="modal-footer">
                <button type="button" className="btn btn-secondary" onClick={() => setShowModal(false)}>Batal</button>
                <button type="submit" className="btn btn-primary">Buat Tugas</button>
              </div>
            </form>
          </div>
        </div>
      )}
    </>
  );
}
