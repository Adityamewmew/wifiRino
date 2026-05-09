import { useState, useEffect } from 'react';
import api from '../../api/client';

export default function PengaturanPage() {
  const [settings, setSettings] = useState({});
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [message, setMessage] = useState('');

  useEffect(() => { fetchSettings(); }, []);

  const fetchSettings = async () => {
    try {
      const res = await api.get('/pengaturan');
      setSettings(res.data.data || {});
    } catch (err) { console.error(err); }
    finally { setLoading(false); }
  };

  const handleChange = (key, value) => {
    setSettings(prev => ({ ...prev, [key]: value }));
  };

  const handleSave = async () => {
    setSaving(true);
    setMessage('');
    try {
      await api.post('/pengaturan', settings);
      setMessage('Pengaturan berhasil disimpan!');
      setTimeout(() => setMessage(''), 3000);
    } catch (err) {
      setMessage('Gagal menyimpan: ' + (err.response?.data?.message || err.message));
    } finally { setSaving(false); }
  };

  if (loading) return <div className="loading-center"><div className="spinner" /></div>;

  const sections = [
    {
      title: '💳 Metode Pembayaran — BCA',
      fields: [
        { key: 'payment_bca_no', label: 'Nomor Rekening BCA' },
        { key: 'payment_bca_nama', label: 'Nama Pemilik BCA' },
        { key: 'payment_bca_aktif', label: 'Aktif', type: 'toggle' },
      ],
    },
    {
      title: '💳 Metode Pembayaran — Mandiri',
      fields: [
        { key: 'payment_mdr_no', label: 'Nomor Rekening Mandiri' },
        { key: 'payment_mdr_nama', label: 'Nama Pemilik Mandiri' },
        { key: 'payment_mdr_aktif', label: 'Aktif', type: 'toggle' },
      ],
    },
    {
      title: '💳 Metode Pembayaran — DANA',
      fields: [
        { key: 'payment_dana_no', label: 'Nomor DANA' },
        { key: 'payment_dana_nama', label: 'Nama Pemilik DANA' },
        { key: 'payment_dana_aktif', label: 'Aktif', type: 'toggle' },
      ],
    },
    {
      title: '📱 WhatsApp',
      fields: [
        { key: 'payment_wa_cs', label: 'Nomor WA Customer Service' },
      ],
    },
  ];

  return (
    <>
      <div className="page-header">
        <div className="page-header-left">
          <h1>Pengaturan</h1>
          <p>Konfigurasi sistem dan metode pembayaran</p>
        </div>
        <button className="btn btn-primary" onClick={handleSave} disabled={saving} id="save-settings-btn">
          {saving ? 'Menyimpan...' : '💾 Simpan Pengaturan'}
        </button>
      </div>

      {message && (
        <div style={{
          marginBottom: 16, padding: '12px 16px', borderRadius: 8,
          background: message.includes('Gagal') ? 'var(--color-danger-bg)' : 'var(--color-success-bg)',
          color: message.includes('Gagal') ? 'var(--color-danger)' : 'var(--color-success)',
          fontSize: 13, fontWeight: 500,
        }}>
          {message}
        </div>
      )}

      <div style={{ display: 'grid', gap: 16, maxWidth: 640 }}>
        {sections.map((section, i) => (
          <div className="glass-card" key={i}>
            <h3 style={{ fontSize: 15, marginBottom: 16 }}>{section.title}</h3>
            {section.fields.map(field => (
              <div className="form-group" key={field.key}>
                <label className="form-label">{field.label}</label>
                {field.type === 'toggle' ? (
                  <div style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                    <button
                      type="button"
                      className={`btn btn-sm ${settings[field.key] === '1' ? 'btn-primary' : 'btn-secondary'}`}
                      onClick={() => handleChange(field.key, settings[field.key] === '1' ? '0' : '1')}
                      style={{ minWidth: 72 }}
                    >
                      {settings[field.key] === '1' ? '✅ Aktif' : '❌ Nonaktif'}
                    </button>
                  </div>
                ) : (
                  <input
                    className="form-input"
                    value={settings[field.key] || ''}
                    onChange={(e) => handleChange(field.key, e.target.value)}
                  />
                )}
              </div>
            ))}
          </div>
        ))}
      </div>
    </>
  );
}
