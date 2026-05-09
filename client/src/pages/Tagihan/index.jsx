import { useState, useEffect, useCallback } from 'react';
import api from '../../api/client';
import { formatRupiah, BULAN_NAMA } from '../../utils/format';

export default function TagihanPage() {
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(false);
  const now = new Date();
  const [bulan, setBulan] = useState(now.getMonth() + 1);
  const [tahun, setTahun] = useState(now.getFullYear());
  const [search, setSearch] = useState('');
  const [page, setPage] = useState(1);
  const [total, setTotal] = useState(0);
  const limit = 20;

  const fetchData = useCallback(async () => {
    setLoading(true);
    try {
      const res = await api.get('/tagihan', { params: { bulan, tahun, search, page, limit } });
      setData(res.data.data || []);
      setTotal(res.data.total || 0);
    } catch (err) { console.error(err); }
    finally { setLoading(false); }
  }, [bulan, tahun, search, page]);

  useEffect(() => { fetchData(); }, [fetchData]);

  const handleSync = async () => {
    try {
      await api.get(`/tagihan/sync?bulan=${bulan}&tahun=${tahun}`);
      fetchData();
    } catch (err) { console.error(err); }
  };

  const totalPages = Math.ceil(total / limit);

  return (
    <>
      <div className="page-header">
        <div className="page-header-left">
          <h1>Tagihan</h1>
          <p>{BULAN_NAMA[bulan - 1]} {tahun} — {total} tagihan</p>
        </div>
        <div style={{ display: 'flex', gap: 8 }}>
          <button className="btn btn-secondary" onClick={handleSync} id="sync-tagihan-btn">
            🔄 Sync Tagihan
          </button>
        </div>
      </div>

      <div className="search-bar">
        <select className="form-input" style={{ width: 140 }} value={bulan} onChange={(e) => { setBulan(+e.target.value); setPage(1); }}>
          {BULAN_NAMA.map((n, i) => <option key={i} value={i + 1}>{n}</option>)}
        </select>
        <input type="number" className="form-input" style={{ width: 100 }} value={tahun} onChange={(e) => { setTahun(+e.target.value); setPage(1); }} />
        <div className="search-input-wrapper" style={{ flex: 1 }}>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
          </svg>
          <input className="search-input" placeholder="Cari pelanggan..." value={search} onChange={(e) => { setSearch(e.target.value); setPage(1); }} />
        </div>
      </div>

      <div className="glass-card" style={{ padding: 0, overflow: 'hidden' }}>
        {loading ? (
          <div className="loading-center"><div className="spinner" /></div>
        ) : (
          <table className="data-table">
            <thead>
              <tr>
                <th>Pelanggan</th>
                <th>Area</th>
                <th>Paket</th>
                <th>Total</th>
                <th>Status</th>
                <th>Tgl Bayar</th>
              </tr>
            </thead>
            <tbody>
              {data.map((t) => (
                <tr key={t.id}>
                  <td>
                    <div style={{ fontWeight: 500 }}>{t.namaPelanggan || '-'}</div>
                    <div style={{ fontSize: 11, color: 'var(--text-muted)' }}>{t.idPelanggan}</div>
                  </td>
                  <td>{t.area || '-'}</td>
                  <td>{t.paket || '-'}</td>
                  <td style={{ fontWeight: 600 }}>{formatRupiah(t.totalTagihan)}</td>
                  <td>
                    <span className={`badge badge-${t.status === 'lunas' ? 'success' : 'warning'}`}>
                      {t.status || 'belum'}
                    </span>
                  </td>
                  <td style={{ fontSize: 12 }}>{t.tglBayar ? new Date(t.tglBayar).toLocaleDateString('id-ID') : '-'}</td>
                </tr>
              ))}
              {!data.length && (
                <tr><td colSpan="6" style={{ textAlign: 'center', padding: 40, color: 'var(--text-muted)' }}>Tidak ada tagihan</td></tr>
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
    </>
  );
}
