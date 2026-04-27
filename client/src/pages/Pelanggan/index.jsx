import { useState, useEffect, useCallback } from 'react';
import api from '../../api/client';
import { formatRupiah } from '../../utils/format';

export default function PelangganPage() {
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(false);
  const [search, setSearch] = useState('');
  const [page, setPage] = useState(1);
  const [total, setTotal] = useState(0);
  const limit = 20;

  // Modal State
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [submitting, setSubmitting] = useState(false);
  const [formData, setFormData] = useState({
    idPelanggan: '',
    nama: '',
    noWA: '',
    area: '',
    paket: '',
    hargaPaket: '',
    tglTagih: 10,
    alamat: '',
    status: 'aktif'
  });

  const fetchData = useCallback(async () => {
    setLoading(true);
    try {
      const res = await api.get('/pelanggan', { params: { search, page, limit } });
      setData(res.data.data || []);
      setTotal(res.data.total || 0);
    } catch (err) { console.error(err); }
    finally { setLoading(false); }
  }, [search, page]);

  useEffect(() => { fetchData(); }, [fetchData]);

  const totalPages = Math.ceil(total / limit);

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({ ...prev, [name]: value }));
  };

  const handleAddSubmit = async (e) => {
    e.preventDefault();
    setSubmitting(true);
    try {
      await api.post('/collections/pelanggan', formData);
      setIsModalOpen(false);
      setFormData({
        idPelanggan: '', nama: '', noWA: '', area: '', paket: '', hargaPaket: '', tglTagih: 10, alamat: '', status: 'aktif'
      });
      fetchData(); // Refresh table
    } catch (err) {
      console.error(err);
      alert('Gagal menambah pelanggan: ' + (err.response?.data?.message || err.message));
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <>
      <div className="page-header">
        <div className="page-header-left">
          <h1>Pelanggan</h1>
          <p>{total} pelanggan terdaftar</p>
        </div>
        <button className="btn btn-primary" onClick={() => setIsModalOpen(true)}>
          + Tambah Pelanggan
        </button>
      </div>

      <div className="search-bar">
        <div className="search-input-wrapper">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
          </svg>
          <input
            className="search-input"
            placeholder="Cari pelanggan..."
            value={search}
            onChange={(e) => { setSearch(e.target.value); setPage(1); }}
          />
        </div>
      </div>

      <div className="glass-card" style={{ padding: 0, overflow: 'hidden' }}>
        {loading ? (
          <div className="loading-center"><div className="spinner" /></div>
        ) : (
          <table className="data-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Area</th>
                <th>Paket</th>
                <th>Harga</th>
                <th>Status</th>
                <th>No. WA</th>
              </tr>
            </thead>
            <tbody>
              {data.map((pel) => (
                <tr key={pel.id}>
                  <td style={{ fontFamily: 'monospace', fontSize: 12 }}>{pel.idPelanggan || '-'}</td>
                  <td style={{ fontWeight: 500 }}>{pel.nama || '-'}</td>
                  <td>{pel.area || '-'}</td>
                  <td>{pel.paket || '-'}</td>
                  <td>{formatRupiah(pel.totalFinal || pel.hargaPaket)}</td>
                  <td>
                    <span className={`badge badge-${pel.status === 'aktif' ? 'success' : 'danger'}`}>
                      {pel.status || 'aktif'}
                    </span>
                  </td>
                  <td>{pel.noWA || '-'}</td>
                </tr>
              ))}
              {!data.length && (
                <tr><td colSpan="7" style={{ textAlign: 'center', padding: 40, color: 'var(--text-muted)' }}>Tidak ada data</td></tr>
              )}
            </tbody>
          </table>
        )}
      </div>

      {totalPages > 1 && (
        <div className="pagination">
          <span className="pagination-info">Hal {page} dari {totalPages}</span>
          <button className="pagination-btn" disabled={page <= 1} onClick={() => setPage(p => p - 1)}>←</button>
          <button className="pagination-btn" disabled={page >= totalPages} onClick={() => setPage(p => p + 1)}>→</button>
        </div>
      )}

      {/* Modal Tambah Pelanggan */}
      {isModalOpen && (
        <div className="modal-overlay">
          <div className="modal-content glass-card">
            <div className="modal-header">
              <h3>Tambah Pelanggan Baru</h3>
              <button className="btn-ghost" onClick={() => setIsModalOpen(false)} style={{ padding: 4 }}>✕</button>
            </div>
            
            <form onSubmit={handleAddSubmit}>
              <div className="modal-body">
                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16, marginBottom: 16 }}>
                  <div>
                    <label className="form-label">ID Pelanggan</label>
                    <input required className="form-input" name="idPelanggan" value={formData.idPelanggan} onChange={handleInputChange} placeholder="Misal: 11200" />
                  </div>
                  <div>
                    <label className="form-label">Nama Lengkap</label>
                    <input required className="form-input" name="nama" value={formData.nama} onChange={handleInputChange} placeholder="Nama Pelanggan" />
                  </div>
                </div>
                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16, marginBottom: 16 }}>
                  <div>
                    <label className="form-label">No. WhatsApp</label>
                    <input className="form-input" name="noWA" value={formData.noWA} onChange={handleInputChange} placeholder="08..." />
                  </div>
                  <div>
                    <label className="form-label">Area / Wilayah</label>
                    <input className="form-input" name="area" value={formData.area} onChange={handleInputChange} placeholder="Area" />
                  </div>
                </div>
                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16, marginBottom: 16 }}>
                  <div>
                    <label className="form-label">Nama Paket</label>
                    <input required className="form-input" name="paket" value={formData.paket} onChange={handleInputChange} placeholder="Contoh: Super 20Mbps" />
                  </div>
                  <div>
                    <label className="form-label">Harga Paket (Rp)</label>
                    <input required type="number" className="form-input" name="hargaPaket" value={formData.hargaPaket} onChange={handleInputChange} placeholder="Misal: 150000" />
                  </div>
                </div>
                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16, marginBottom: 16 }}>
                  <div>
                    <label className="form-label">Tanggal Tagih</label>
                    <input required type="number" className="form-input" name="tglTagih" min="1" max="31" value={formData.tglTagih} onChange={handleInputChange} />
                  </div>
                  <div>
                    <label className="form-label">Status</label>
                    <select className="form-input" name="status" value={formData.status} onChange={handleInputChange}>
                      <option value="aktif">Aktif</option>
                      <option value="nonaktif">Non-Aktif</option>
                      <option value="isolir">Isolir</option>
                    </select>
                  </div>
                </div>
                <div>
                  <label className="form-label">Alamat Lengkap</label>
                  <textarea className="form-input" name="alamat" rows="2" value={formData.alamat} onChange={handleInputChange} placeholder="Alamat rumah..." />
                </div>
              </div>
              
              <div className="modal-footer">
                <button type="button" className="btn btn-secondary" onClick={() => setIsModalOpen(false)}>Batal</button>
                <button type="submit" className="btn btn-primary" disabled={submitting}>
                  {submitting ? 'Menyimpan...' : 'Simpan Pelanggan'}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </>
  );
}
