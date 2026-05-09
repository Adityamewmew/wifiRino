import { useState, useEffect } from 'react';
import api from '../../api/client';
import { formatRupiah } from '../../utils/format';

export default function PaketPage() {
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(false);

  useEffect(() => { fetchData(); }, []);

  const fetchData = async () => {
    setLoading(true);
    try {
      const res = await api.get('/paket');
      setData(res.data.data || []);
    } catch (err) { console.error(err); }
    finally { setLoading(false); }
  };

  return (
    <>
      <div className="page-header">
        <div className="page-header-left">
          <h1>Paket Internet</h1>
          <p>{data.length} paket terdaftar</p>
        </div>
        <button className="btn btn-primary" id="add-paket-btn">+ Tambah Paket</button>
      </div>

      <div className="stats-row">
        {data.map((paket) => (
          <div className="stat-card" key={paket.id}>
            <div className="stat-card-icon" style={{ background: 'var(--color-info-bg)', color: 'var(--color-info)' }}>
              📦
            </div>
            <div className="stat-card-value" style={{ fontSize: 18 }}>{paket.nama}</div>
            <div className="stat-card-label" style={{ fontSize: 16, fontWeight: 600, color: 'var(--color-primary)', marginTop: 4 }}>
              {formatRupiah(paket.harga)}
            </div>
            <div style={{ marginTop: 8 }}>
              <span className={`badge badge-${paket.aktif ? 'success' : 'danger'}`}>
                {paket.aktif ? 'Aktif' : 'Nonaktif'}
              </span>
            </div>
            {paket.deskripsi && (
              <p style={{ fontSize: 12, color: 'var(--text-muted)', marginTop: 8 }}>{paket.deskripsi}</p>
            )}
          </div>
        ))}
      </div>

      {loading && <div className="loading-center"><div className="spinner" /></div>}
      {!loading && !data.length && (
        <div className="glass-card" style={{ textAlign: 'center', padding: 60, color: 'var(--text-muted)' }}>
          Belum ada paket. Klik "Tambah Paket" untuk mulai.
        </div>
      )}
    </>
  );
}
