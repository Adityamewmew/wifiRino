import { useState, useEffect } from 'react';
import api from '../../api/client';
import { formatRupiah, BULAN_NAMA } from '../../utils/format';

export default function DashboardPage() {
  const [stats, setStats] = useState(null);
  const [loading, setLoading] = useState(true);
  const now = new Date();
  const [bulan] = useState(now.getMonth() + 1);
  const [tahun] = useState(now.getFullYear());

  useEffect(() => {
    fetchStats();
  }, [bulan, tahun]);

  const fetchStats = async () => {
    setLoading(true);
    try {
      const res = await api.get(`/stats/keuangan?bulan=${bulan}&tahun=${tahun}`);
      setStats(res.data.data);
    } catch (err) {
      console.error('Failed to load stats:', err);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return <div className="loading-center"><div className="spinner" /></div>;
  }

  const cards = [
    { label: 'Pelanggan Aktif', value: stats?.totalPelangganAktif || 0, icon: '👥', color: '#1D546C', bg: '#E8F4F8' },
    { label: 'Tagihan Lunas', value: stats?.lunas || 0, icon: '✅', color: '#059669', bg: '#ECFDF5' },
    { label: 'Belum Bayar', value: stats?.belumBayar || 0, icon: '⏳', color: '#D97706', bg: '#FFFBEB' },
    { label: 'Pemasukan', value: formatRupiah(stats?.pemasukan || 0), icon: '💰', color: '#059669', bg: '#ECFDF5' },
    { label: 'Pengeluaran', value: formatRupiah(stats?.pengeluaran || 0), icon: '📤', color: '#DC2626', bg: '#FEF2F2' },
    { label: 'Saldo', value: formatRupiah(stats?.saldo || 0), icon: '🏦', color: '#1D546C', bg: '#E8F4F8' },
  ];

  return (
    <>
      <div className="page-header">
        <div className="page-header-left">
          <h1>Dashboard</h1>
          <p>Ringkasan {BULAN_NAMA[bulan - 1]} {tahun}</p>
        </div>
      </div>

      <div className="stats-row">
        {cards.map((card, i) => (
          <div className="stat-card" key={i}>
            <div className="stat-card-icon" style={{ background: card.bg, color: card.color }}>
              {card.icon}
            </div>
            <div className="stat-card-value">{card.value}</div>
            <div className="stat-card-label">{card.label}</div>
          </div>
        ))}
      </div>

      <div className="glass-card">
        <h3 style={{ marginBottom: 16, fontSize: 16, color: 'var(--text-primary)' }}>
          Ringkasan Tagihan — {BULAN_NAMA[bulan - 1]} {tahun}
        </h3>
        <div style={{ display: 'flex', gap: 24, flexWrap: 'wrap' }}>
          <div style={{ flex: 1, minWidth: 200 }}>
            <div style={{ fontSize: 13, color: 'var(--text-secondary)', marginBottom: 8 }}>
              Total Tagihan Terbit
            </div>
            <div style={{ fontSize: 28, fontWeight: 700, color: 'var(--text-primary)' }}>
              {stats?.totalTagihan || 0}
            </div>
          </div>
          <div style={{ flex: 1, minWidth: 200 }}>
            <div style={{ fontSize: 13, color: 'var(--text-secondary)', marginBottom: 8 }}>
              Tingkat Pembayaran
            </div>
            <div style={{ fontSize: 28, fontWeight: 700, color: 'var(--color-success)' }}>
              {stats?.totalTagihan ? Math.round((stats.lunas / stats.totalTagihan) * 100) : 0}%
            </div>
            <div style={{
              height: 6, borderRadius: 3, background: 'rgba(5,150,105,0.15)', marginTop: 8,
              overflow: 'hidden',
            }}>
              <div style={{
                height: '100%', borderRadius: 3,
                background: 'var(--color-success)',
                width: `${stats?.totalTagihan ? Math.round((stats.lunas / stats.totalTagihan) * 100) : 0}%`,
                transition: 'width 0.5s ease',
              }} />
            </div>
          </div>
        </div>
      </div>
    </>
  );
}
