import { useState, useEffect, useCallback } from 'react';
import api from '../../api/client';
import { formatRupiah, BULAN_NAMA } from '../../utils/format';
import useAuthStore from '../../store/useAuthStore';

export default function TagihPelangganPage() {
  const { user } = useAuthStore();
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(false);
  const [search, setSearch] = useState('');
  const now = new Date();
  const [bulan, setBulan] = useState(now.getMonth() + 1);
  const [tahun, setTahun] = useState(now.getFullYear());

  const fetchData = useCallback(async () => {
    setLoading(true);
    try {
      const res = await api.get('/tagihan', { params: { bulan, tahun, status: 'belum_bayar', limit: 500 } });
      setData(res.data.data || []);
    } catch (err) { console.error(err); }
    finally { setLoading(false); }
  }, [bulan, tahun]);

  useEffect(() => { fetchData(); }, [fetchData]);

  const handleBayar = async (tagihan) => {
    if (!confirm(`Konfirmasi pembayaran ${tagihan.namaPelanggan} - ${formatRupiah(tagihan.totalTagihan)}?`)) return;
    try {
      await api.post(`/tagihan/${tagihan.id}/bayar`, {
        metodeBayar: 'tunai',
        dibayar_ke: user?.nama || 'Penagih',
      });
      fetchData();
    } catch (err) { console.error(err); alert('Gagal: ' + (err.response?.data?.message || err.message)); }
  };

  const filtered = data.filter(t => {
    if (!search) return true;
    const q = search.toLowerCase();
    return (t.namaPelanggan || '').toLowerCase().includes(q) || (t.area || '').toLowerCase().includes(q);
  });

  const totalNominal = filtered.reduce((s, t) => s + Number(t.totalTagihan || 0), 0);

  return (
    <>
      <div className="page-header">
        <div className="page-header-left">
          <h1>🏠 Mode Tagih Pelanggan</h1>
          <p>{BULAN_NAMA[bulan - 1]} {tahun} — {filtered.length} pelanggan belum bayar</p>
        </div>
      </div>

      <div className="stats-row" style={{ gridTemplateColumns: 'repeat(2, 1fr)' }}>
        <div className="stat-card">
          <div className="stat-card-icon" style={{ background: '#FEF2F2', color: '#DC2626' }}>📄</div>
          <div className="stat-card-value">{filtered.length}</div>
          <div className="stat-card-label">Belum Bayar</div>
        </div>
        <div className="stat-card">
          <div className="stat-card-icon" style={{ background: '#FFFBEB', color: '#D97706' }}>💰</div>
          <div className="stat-card-value" style={{ fontSize: 18 }}>{formatRupiah(totalNominal)}</div>
          <div className="stat-card-label">Total Nominal</div>
        </div>
      </div>

      <div className="search-bar">
        <select className="form-input" style={{ width: 140 }} value={bulan} onChange={(e) => setBulan(+e.target.value)}>
          {BULAN_NAMA.map((n, i) => <option key={i} value={i + 1}>{n}</option>)}
        </select>
        <input type="number" className="form-input" style={{ width: 100 }} value={tahun} onChange={(e) => setTahun(+e.target.value)} />
        <div className="search-input-wrapper" style={{ flex: 1 }}>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          <input className="search-input" placeholder="Cari pelanggan / area..." value={search} onChange={(e) => setSearch(e.target.value)} />
        </div>
      </div>

      {loading ? (
        <div className="loading-center"><div className="spinner" /></div>
      ) : (
        <div style={{ display: 'grid', gap: 10 }}>
          {filtered.map(t => (
            <div className="glass-card" key={t.id} style={{ padding: 16, display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
              <div>
                <div style={{ fontWeight: 600, fontSize: 15, marginBottom: 4 }}>{t.namaPelanggan || '-'}</div>
                <div style={{ display: 'flex', gap: 12, fontSize: 12, color: 'var(--text-muted)' }}>
                  <span>📍 {t.area || '-'}</span>
                  <span>📦 {t.paket || '-'}</span>
                  <span>ID: {t.idPelanggan || '-'}</span>
                </div>
              </div>
              <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
                <div style={{ textAlign: 'right' }}>
                  <div style={{ fontSize: 18, fontWeight: 700, color: 'var(--color-danger)' }}>{formatRupiah(t.totalTagihan)}</div>
                </div>
                <button className="btn btn-primary btn-sm" onClick={() => handleBayar(t)} style={{ whiteSpace: 'nowrap' }}>
                  ✅ Bayar
                </button>
              </div>
            </div>
          ))}
          {!filtered.length && (
            <div className="glass-card" style={{ textAlign: 'center', padding: 60, color: 'var(--text-muted)' }}>
              Semua pelanggan sudah bayar! 🎉
            </div>
          )}
        </div>
      )}
    </>
  );
}
