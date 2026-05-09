import { useState, useEffect, useCallback } from 'react';
import api from '../../api/client';
import { formatRupiah, BULAN_NAMA } from '../../utils/format';

export default function BukuKasPage() {
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(false);
  const [showModal, setShowModal] = useState(false);
  const now = new Date();
  const [bulan, setBulan] = useState(now.getMonth() + 1);
  const [tahun, setTahun] = useState(now.getFullYear());
  const [page, setPage] = useState(1);
  const [total, setTotal] = useState(0);
  const limit = 25;

  // Form state
  const [form, setForm] = useState({ jenis: 'pemasukan', kategori: '', nominal: '', keterangan: '' });

  const fetchData = useCallback(async () => {
    setLoading(true);
    try {
      const res = await api.get('/buku-kas', { params: { bulan, tahun, page, limit } });
      setData(res.data.data || []);
      setTotal(res.data.total || 0);
    } catch (err) { console.error(err); }
    finally { setLoading(false); }
  }, [bulan, tahun, page]);

  useEffect(() => { fetchData(); }, [fetchData]);

  const totalPages = Math.ceil(total / limit);

  // Calculate summary
  const pemasukan = data.filter(d => d.jenis === 'pemasukan').reduce((s, d) => s + Number(d.nominal || 0), 0);
  const pengeluaran = data.filter(d => d.jenis === 'pengeluaran').reduce((s, d) => s + Number(d.nominal || 0), 0);

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      await api.post('/buku-kas', {
        tanggal: new Date().toISOString(),
        jenis: form.jenis,
        kategori: form.kategori,
        nominal: parseFloat(form.nominal) || 0,
        keterangan: form.keterangan,
      });
      setShowModal(false);
      setForm({ jenis: 'pemasukan', kategori: '', nominal: '', keterangan: '' });
      fetchData();
    } catch (err) { console.error(err); }
  };

  const handleDelete = async (id) => {
    if (!confirm('Hapus transaksi ini?')) return;
    try {
      await api.delete(`/buku-kas/${id}`);
      fetchData();
    } catch (err) { console.error(err); }
  };

  return (
    <>
      <div className="page-header">
        <div className="page-header-left">
          <h1>Buku Kas</h1>
          <p>Pencatatan transaksi {BULAN_NAMA[bulan - 1]} {tahun}</p>
        </div>
        <button className="btn btn-primary" onClick={() => setShowModal(true)} id="add-transaksi-btn">
          + Tambah Transaksi
        </button>
      </div>

      {/* Summary Cards */}
      <div className="stats-row" style={{ gridTemplateColumns: 'repeat(3, 1fr)' }}>
        <div className="stat-card">
          <div className="stat-card-icon" style={{ background: '#ECFDF5', color: '#059669' }}>💰</div>
          <div className="stat-card-value" style={{ color: 'var(--color-success)' }}>{formatRupiah(pemasukan)}</div>
          <div className="stat-card-label">Total Pemasukan</div>
        </div>
        <div className="stat-card">
          <div className="stat-card-icon" style={{ background: '#FEF2F2', color: '#DC2626' }}>📤</div>
          <div className="stat-card-value" style={{ color: 'var(--color-danger)' }}>{formatRupiah(pengeluaran)}</div>
          <div className="stat-card-label">Total Pengeluaran</div>
        </div>
        <div className="stat-card">
          <div className="stat-card-icon" style={{ background: '#E8F4F8', color: '#1D546C' }}>🏦</div>
          <div className="stat-card-value">{formatRupiah(pemasukan - pengeluaran)}</div>
          <div className="stat-card-label">Saldo</div>
        </div>
      </div>

      {/* Filters */}
      <div className="search-bar">
        <select className="form-input" style={{ width: 140 }} value={bulan} onChange={(e) => { setBulan(+e.target.value); setPage(1); }}>
          {BULAN_NAMA.map((n, i) => <option key={i} value={i + 1}>{n}</option>)}
        </select>
        <input type="number" className="form-input" style={{ width: 100 }} value={tahun} onChange={(e) => { setTahun(+e.target.value); setPage(1); }} />
      </div>

      {/* Table */}
      <div className="glass-card" style={{ padding: 0, overflow: 'hidden' }}>
        {loading ? (
          <div className="loading-center"><div className="spinner" /></div>
        ) : (
          <table className="data-table">
            <thead>
              <tr>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Kategori</th>
                <th>Keterangan</th>
                <th style={{ textAlign: 'right' }}>Nominal</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              {data.map((item) => (
                <tr key={item.id}>
                  <td style={{ fontSize: 12 }}>{item.tanggal ? new Date(item.tanggal).toLocaleDateString('id-ID') : '-'}</td>
                  <td>
                    <span className={`badge badge-${item.jenis === 'pemasukan' ? 'success' : 'danger'}`}>
                      {item.jenis === 'pemasukan' ? '↗ Masuk' : '↙ Keluar'}
                    </span>
                  </td>
                  <td style={{ fontWeight: 500 }}>{item.kategori || '-'}</td>
                  <td style={{ fontSize: 12, maxWidth: 300, overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>
                    {item.keterangan || '-'}
                  </td>
                  <td style={{ textAlign: 'right', fontWeight: 600, color: item.jenis === 'pemasukan' ? 'var(--color-success)' : 'var(--color-danger)' }}>
                    {item.jenis === 'pemasukan' ? '+' : '-'}{formatRupiah(item.nominal)}
                  </td>
                  <td>
                    <button className="btn btn-ghost btn-sm" onClick={() => handleDelete(item.id)} title="Hapus">🗑️</button>
                  </td>
                </tr>
              ))}
              {!data.length && (
                <tr><td colSpan="6" style={{ textAlign: 'center', padding: 40, color: 'var(--text-muted)' }}>Belum ada transaksi</td></tr>
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

      {/* Modal Tambah Transaksi */}
      {showModal && (
        <div className="modal-overlay" onClick={() => setShowModal(false)}>
          <div className="modal-content" onClick={(e) => e.stopPropagation()}>
            <div className="modal-header">
              <h3>Tambah Transaksi</h3>
              <button className="btn btn-ghost btn-sm" onClick={() => setShowModal(false)}>✕</button>
            </div>
            <form onSubmit={handleSubmit}>
              <div className="modal-body">
                <div className="form-group">
                  <label className="form-label">Jenis</label>
                  <select className="form-input" value={form.jenis} onChange={(e) => setForm({ ...form, jenis: e.target.value })}>
                    <option value="pemasukan">Pemasukan</option>
                    <option value="pengeluaran">Pengeluaran</option>
                  </select>
                </div>
                <div className="form-group">
                  <label className="form-label">Kategori</label>
                  <input className="form-input" placeholder="Contoh: Tagihan Internet" value={form.kategori} onChange={(e) => setForm({ ...form, kategori: e.target.value })} required />
                </div>
                <div className="form-group">
                  <label className="form-label">Nominal (Rp)</label>
                  <input className="form-input" type="number" placeholder="0" value={form.nominal} onChange={(e) => setForm({ ...form, nominal: e.target.value })} required />
                </div>
                <div className="form-group">
                  <label className="form-label">Keterangan</label>
                  <input className="form-input" placeholder="Detail transaksi..." value={form.keterangan} onChange={(e) => setForm({ ...form, keterangan: e.target.value })} />
                </div>
              </div>
              <div className="modal-footer">
                <button type="button" className="btn btn-secondary" onClick={() => setShowModal(false)}>Batal</button>
                <button type="submit" className="btn btn-primary">Simpan</button>
              </div>
            </form>
          </div>
        </div>
      )}
    </>
  );
}
