import { useState, useEffect } from 'react';
import api from '../../api/client';
import { formatRupiah, BULAN_NAMA } from '../../utils/format';

export default function PembukuanAgenPage() {
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(false);
  const now = new Date();
  const [bulan, setBulan] = useState(now.getMonth() + 1);
  const [tahun, setTahun] = useState(now.getFullYear());

  useEffect(() => { fetchData(); }, [bulan, tahun]);

  const fetchData = async () => {
    setLoading(true);
    try {
      // Get tagihan lunas yang dibayar oleh agen/penagih
      const res = await api.get('/tagihan', { params: { bulan, tahun, status: 'lunas', limit: 500 } });
      const all = res.data.data || [];
      // Group by dibayar_ke (collector)
      const grouped = {};
      all.forEach(t => {
        const collector = t.dibayar_ke || t.metodeBayar || 'Langsung';
        if (!grouped[collector]) grouped[collector] = { nama: collector, count: 0, total: 0, items: [] };
        grouped[collector].count++;
        grouped[collector].total += Number(t.totalTagihan || 0);
        grouped[collector].items.push(t);
      });
      setData(Object.values(grouped));
    } catch (err) { console.error(err); }
    finally { setLoading(false); }
  };

  const grandTotal = data.reduce((s, g) => s + g.total, 0);
  const grandCount = data.reduce((s, g) => s + g.count, 0);

  return (
    <>
      <div className="page-header">
        <div className="page-header-left">
          <h1>Pembukuan Agen</h1>
          <p>Rekap tagihan terkumpul per agen — {BULAN_NAMA[bulan - 1]} {tahun}</p>
        </div>
      </div>

      <div className="search-bar">
        <select className="form-input" style={{ width: 140 }} value={bulan} onChange={(e) => setBulan(+e.target.value)}>
          {BULAN_NAMA.map((n, i) => <option key={i} value={i + 1}>{n}</option>)}
        </select>
        <input type="number" className="form-input" style={{ width: 100 }} value={tahun} onChange={(e) => setTahun(+e.target.value)} />
      </div>

      {/* Summary */}
      <div className="stats-row" style={{ gridTemplateColumns: 'repeat(3, 1fr)' }}>
        <div className="stat-card">
          <div className="stat-card-icon" style={{ background: '#E8F4F8', color: '#1D546C' }}>👥</div>
          <div className="stat-card-value">{data.length}</div>
          <div className="stat-card-label">Agen/Penagih</div>
        </div>
        <div className="stat-card">
          <div className="stat-card-icon" style={{ background: '#ECFDF5', color: '#059669' }}>📄</div>
          <div className="stat-card-value">{grandCount}</div>
          <div className="stat-card-label">Total Tagihan Terkumpul</div>
        </div>
        <div className="stat-card">
          <div className="stat-card-icon" style={{ background: '#ECFDF5', color: '#059669' }}>💰</div>
          <div className="stat-card-value" style={{ fontSize: 20 }}>{formatRupiah(grandTotal)}</div>
          <div className="stat-card-label">Total Nominal</div>
        </div>
      </div>

      {loading ? (
        <div className="loading-center"><div className="spinner" /></div>
      ) : (
        <div style={{ display: 'grid', gap: 16 }}>
          {data.map((group, i) => (
            <div className="glass-card" key={i}>
              <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 16 }}>
                <div>
                  <h3 style={{ fontSize: 16, marginBottom: 4 }}>{group.nama}</h3>
                  <span className="badge badge-info">{group.count} tagihan</span>
                </div>
                <div style={{ textAlign: 'right' }}>
                  <div style={{ fontSize: 20, fontWeight: 700, color: 'var(--color-success)' }}>{formatRupiah(group.total)}</div>
                </div>
              </div>
              <table className="data-table">
                <thead>
                  <tr>
                    <th>Pelanggan</th>
                    <th>Area</th>
                    <th>Paket</th>
                    <th style={{ textAlign: 'right' }}>Nominal</th>
                    <th>Tgl Bayar</th>
                  </tr>
                </thead>
                <tbody>
                  {group.items.slice(0, 10).map(t => (
                    <tr key={t.id}>
                      <td style={{ fontWeight: 500 }}>{t.namaPelanggan || '-'}</td>
                      <td>{t.area || '-'}</td>
                      <td>{t.paket || '-'}</td>
                      <td style={{ textAlign: 'right', fontWeight: 600 }}>{formatRupiah(t.totalTagihan)}</td>
                      <td style={{ fontSize: 12 }}>{t.tglBayar ? new Date(t.tglBayar).toLocaleDateString('id-ID') : '-'}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
              {group.items.length > 10 && (
                <div style={{ padding: '12px 16px', fontSize: 12, color: 'var(--text-muted)' }}>
                  ... dan {group.items.length - 10} tagihan lainnya
                </div>
              )}
            </div>
          ))}
          {!data.length && (
            <div className="glass-card" style={{ textAlign: 'center', padding: 60, color: 'var(--text-muted)' }}>
              Tidak ada data pembukuan agen untuk periode ini.
            </div>
          )}
        </div>
      )}
    </>
  );
}
