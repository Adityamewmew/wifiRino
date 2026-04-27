import { useState, useEffect } from 'react';
import { useSearchParams } from 'react-router-dom';
import api from '../../api/client';
import { formatRupiah, BULAN_NAMA } from '../../utils/format';
import useAuthStore from '../../store/useAuthStore';
import './struk.css';

export default function StrukPage() {
  const [searchParams] = useSearchParams();
  const tagihanId = searchParams.get('id');
  const { user } = useAuthStore();
  const [tagihan, setTagihan] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    if (tagihanId) loadTagihan();
    else setError('ID Tagihan tidak ditemukan.');
  }, [tagihanId]);

  const loadTagihan = async () => {
    try {
      const res = await api.get(`/collections/tagihan_bulanan/${tagihanId}`);
      setTagihan(res.data.data || res.data);
    } catch (err) {
      setError('Gagal memuat data struk.');
    } finally { setLoading(false); }
  };

  if (loading) return <div className="loading-center"><div className="spinner" /></div>;
  if (error) return <div className="glass-card" style={{ textAlign: 'center', padding: 40, color: 'var(--color-danger)' }}>{error}</div>;
  if (!tagihan) return null;

  const tglBayar = tagihan.tglBayar ? new Date(tagihan.tglBayar) : new Date();
  const periodeBulan = BULAN_NAMA[(tagihan.bulan || 1) - 1] || tagihan.bulan;

  return (
    <>
      <div className="page-header">
        <div className="page-header-left">
          <h1>🧾 Struk Pembayaran</h1>
        </div>
        <button className="btn btn-primary" onClick={() => window.print()}>🖨️ Cetak Struk</button>
      </div>

      <div className="struk-wrapper">
        <div className="struk-receipt" id="receipt-content">
          <div className="struk-header">
            <h2>⚡ RINONET INTERNET</h2>
            <p>Layanan Internet Terpercaya</p>
          </div>

          <div className="struk-divider" />

          <div className="struk-row">
            <span>No. TRX:</span>
            <strong>#{String(tagihan.id).substring(0, 8).toUpperCase()}</strong>
          </div>
          <div className="struk-row">
            <span>Tanggal:</span>
            <strong>{tglBayar.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</strong>
          </div>
          <div className="struk-row">
            <span>Kasir:</span>
            <strong>{user?.nama || 'Admin'}</strong>
          </div>

          <div className="struk-divider" />

          <div className="struk-row">
            <span>Pelanggan:</span>
            <strong>{tagihan.namaPelanggan || '-'}</strong>
          </div>
          <div className="struk-row">
            <span>ID Pel:</span>
            <strong>{tagihan.idPelanggan || '-'}</strong>
          </div>
          <div className="struk-row">
            <span>Area:</span>
            <strong>{tagihan.area || '-'}</strong>
          </div>

          <div className="struk-divider" />

          <div style={{ fontWeight: 700, fontSize: 12, marginBottom: 6 }}>Deskripsi Tagihan:</div>
          <div className="struk-row">
            <span>Iuran Internet {periodeBulan} {tagihan.tahun} ({tagihan.paket || '-'})</span>
            <strong>{formatRupiah(tagihan.totalTagihan)}</strong>
          </div>

          <div className="struk-divider" style={{ borderTopStyle: 'solid' }} />

          <div className="struk-total">
            <span>TOTAL:</span>
            <span>{formatRupiah(tagihan.totalTagihan)}</span>
          </div>

          <div className="struk-row">
            <span>Status:</span>
            <strong style={{ textTransform: 'uppercase' }}>{tagihan.status || '-'}</strong>
          </div>

          <div className="struk-divider" />

          <div className="struk-footer">
            <p>Terima kasih. Internet Lancar, Rezeki Lancar!</p>
          </div>
        </div>
      </div>
    </>
  );
}
