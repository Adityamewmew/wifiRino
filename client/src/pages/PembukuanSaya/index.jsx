import { useState, useEffect } from 'react';
import api from '../../api/client';
import useAuthStore from '../../store/useAuthStore';
import { formatRupiah, BULAN_NAMA } from '../../utils/format';

export default function PembukuanSayaPage() {
  const { user } = useAuthStore();
  const [summary, setSummary] = useState(null);
  const [loading, setLoading] = useState(true);
  const [periode, setPeriode] = useState('current');

  useEffect(() => { loadSummary(); }, [periode]);

  const loadSummary = async () => {
    setLoading(true);
    try {
      const d = new Date();
      if (periode === 'last') d.setMonth(d.getMonth() - 1);
      const bulan = d.getMonth() + 1;
      const tahun = d.getFullYear();
      const res = await api.get(`/pembukuan/agen-summary?bulan=${bulan}&tahun=${tahun}`);
      const rows = res.data.data || [];
      setSummary(rows[0] || null);
    } catch (err) { console.error(err); }
    finally { setLoading(false); }
  };

  return (
    <>
      <div className="page-header">
        <div className="page-header-left">
          <h1>📒 Pembukuan Saya</h1>
          <p>Rekap tagihan yang Anda kumpulkan</p>
        </div>
      </div>

      {/* Hero Card */}
      <div className="glass-card" style={{ background: 'linear-gradient(135deg, var(--color-primary), var(--color-primary-dark))', color: 'white', marginBottom: 20 }}>
        <div style={{ fontSize: 13, opacity: 0.8, fontWeight: 600 }}>Rekap Agen</div>
        <h2 style={{ fontSize: 22, fontWeight: 800, margin: '6px 0 0' }}>{user?.nama || '-'}</h2>
      </div>

      <div className="search-bar" style={{ marginBottom: 20 }}>
        <select className="form-input" style={{ width: 200 }} value={periode} onChange={(e) => setPeriode(e.target.value)}>
          <option value="current">Periode: Bulan Ini</option>
          <option value="last">Periode: Bulan Lalu</option>
        </select>
        <button className="btn btn-secondary btn-sm" onClick={loadSummary}>🔄 Refresh</button>
      </div>

      {loading ? (
        <div className="loading-center"><div className="spinner" /></div>
      ) : (
        <div className="stats-row" style={{ gridTemplateColumns: 'repeat(2, 1fr)' }}>
          <div className="stat-card">
            <div className="stat-card-label">Total Ditagih</div>
            <div className="stat-card-value" style={{ fontSize: 20 }}>{formatRupiah(summary?.totalTagihan || 0)}</div>
          </div>
          <div className="stat-card">
            <div className="stat-card-label">Pelanggan Bayar</div>
            <div className="stat-card-value">{summary?.jumlahPelanggan || 0}</div>
          </div>
          <div className="stat-card">
            <div className="stat-card-label">Sudah Disetor</div>
            <div className="stat-card-value" style={{ fontSize: 20, color: 'var(--color-success)' }}>{formatRupiah(summary?.setor || 0)}</div>
          </div>
          <div className="stat-card">
            <div className="stat-card-label">Sisa Di Tangan</div>
            <div className="stat-card-value" style={{ fontSize: 20, color: 'var(--color-danger)' }}>{formatRupiah(summary?.sisaDiTangan || 0)}</div>
          </div>
          <div className="stat-card" style={{ gridColumn: '1 / -1' }}>
            <div className="stat-card-label">Status Komisi</div>
            <div className="stat-card-value">-</div>
            <div style={{ fontSize: 11, color: 'var(--text-muted)', marginTop: 4 }}>Komisi dinonaktifkan</div>
          </div>
        </div>
      )}

      {!loading && !summary && (
        <div className="glass-card" style={{ textAlign: 'center', padding: 40, color: 'var(--text-muted)', marginTop: 16 }}>
          Belum ada transaksi untuk periode ini.
        </div>
      )}
    </>
  );
}
