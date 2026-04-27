import { useState, useEffect, useCallback } from 'react';
import api from '../../api/client';

export default function AreaPage() {
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(false);
  const [search, setSearch] = useState('');
  const [showModal, setShowModal] = useState(false);
  const [editId, setEditId] = useState(null);
  const [form, setForm] = useState({ nama: '', keterangan: '' });

  const fetchData = useCallback(async () => {
    setLoading(true);
    try {
      const res = await api.get('/collections/areas');
      const list = (res.data.data || res.data || []).sort((a, b) => (a.nama || '').localeCompare(b.nama || ''));
      setData(list);
    } catch (err) { console.error(err); }
    finally { setLoading(false); }
  }, []);

  useEffect(() => { fetchData(); }, [fetchData]);

  const filtered = data.filter(a => {
    if (!search) return true;
    const q = search.toLowerCase();
    return (a.nama || '').toLowerCase().includes(q) || (a.keterangan || '').toLowerCase().includes(q);
  });

  const openAdd = () => { setEditId(null); setForm({ nama: '', keterangan: '' }); setShowModal(true); };
  const openEdit = (item) => { setEditId(item.id); setForm({ nama: item.nama || '', keterangan: item.keterangan || '' }); setShowModal(true); };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      if (editId) {
        await api.put(`/collections/areas/${editId}`, form);
      } else {
        await api.post('/collections/areas', form);
      }
      setShowModal(false);
      fetchData();
    } catch (err) { console.error(err); }
  };

  const handleDelete = async (id) => {
    if (!confirm('Hapus area ini?')) return;
    try { await api.delete(`/collections/areas/${id}`); fetchData(); } catch (err) { console.error(err); }
  };

  return (
    <>
      <div className="page-header">
        <div className="page-header-left">
          <h1>Manajemen Area/Wilayah</h1>
          <p>{data.length} area terdaftar</p>
        </div>
        <button className="btn btn-primary" onClick={openAdd} id="add-area-btn">+ Tambah Area</button>
      </div>

      <div className="search-bar">
        <div className="search-input-wrapper" style={{ flex: 1 }}>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          <input className="search-input" placeholder="Cari area..." value={search} onChange={(e) => setSearch(e.target.value)} />
        </div>
      </div>

      <div className="glass-card" style={{ padding: 0, overflow: 'hidden' }}>
        {loading ? (
          <div className="loading-center"><div className="spinner" /></div>
        ) : (
          <table className="data-table">
            <thead>
              <tr>
                <th style={{ width: 50 }}>No</th>
                <th>Nama Area / Kode</th>
                <th>Keterangan Kelurahan/Desa</th>
                <th style={{ width: 100 }}></th>
              </tr>
            </thead>
            <tbody>
              {filtered.map((area, i) => (
                <tr key={area.id}>
                  <td style={{ fontWeight: 600, color: 'var(--text-muted)' }}>{i + 1}</td>
                  <td style={{ fontWeight: 700, color: '#D97706' }}>{area.nama || '-'}</td>
                  <td style={{ color: 'var(--text-secondary)' }}>{area.keterangan || '-'}</td>
                  <td>
                    <div style={{ display: 'flex', gap: 4, justifyContent: 'flex-end' }}>
                      <button className="btn btn-ghost btn-sm" onClick={() => openEdit(area)} title="Edit">✏️</button>
                      <button className="btn btn-ghost btn-sm" onClick={() => handleDelete(area.id)} title="Hapus">🗑️</button>
                    </div>
                  </td>
                </tr>
              ))}
              {!filtered.length && (
                <tr><td colSpan="4" style={{ textAlign: 'center', padding: 40, color: 'var(--text-muted)' }}>Belum ada area terdaftar.</td></tr>
              )}
            </tbody>
          </table>
        )}
      </div>

      {showModal && (
        <div className="modal-overlay" onClick={() => setShowModal(false)}>
          <div className="modal-content" onClick={(e) => e.stopPropagation()}>
            <div className="modal-header">
              <h3>{editId ? 'Edit Area' : 'Tambah Area Baru'}</h3>
              <button className="btn btn-ghost btn-sm" onClick={() => setShowModal(false)}>✕</button>
            </div>
            <form onSubmit={handleSubmit}>
              <div className="modal-body">
                <div className="form-group">
                  <label className="form-label">Nama / Kode Area</label>
                  <input className="form-input" value={form.nama} onChange={(e) => setForm({ ...form, nama: e.target.value })} required minLength={2} />
                </div>
                <div className="form-group">
                  <label className="form-label">Keterangan Lokasi</label>
                  <input className="form-input" value={form.keterangan} onChange={(e) => setForm({ ...form, keterangan: e.target.value })} placeholder="Kelurahan/Desa..." />
                </div>
              </div>
              <div className="modal-footer">
                <button type="button" className="btn btn-secondary" onClick={() => setShowModal(false)}>Batal</button>
                <button type="submit" className="btn btn-primary">Simpan Area</button>
              </div>
            </form>
          </div>
        </div>
      )}
    </>
  );
}
