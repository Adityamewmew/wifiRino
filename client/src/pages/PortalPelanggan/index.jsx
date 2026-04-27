import { useState, useEffect, useRef } from 'react';
import api from '../../api/client';
import { formatRupiah, BULAN_NAMA } from '../../utils/format';
import './portal.css';

export default function PortalPelangganPage() {
  const [loading, setLoading] = useState(true);
  const [pelanggan, setPelanggan] = useState(null);
  const [tagihan, setTagihan] = useState(null);
  const [paket, setPaket] = useState([]);
  const [pengumuman, setPengumuman] = useState([]);
  const [loginForm, setLoginForm] = useState({ idPelanggan: '' });
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const [err, setErr] = useState('');

  // CHAT STATE
  const [chatOpen, setChatOpen] = useState(false);
  const [chatMessages, setChatMessages] = useState([]);
  const [chatInput, setChatInput] = useState('');
  const messagesEndRef = useRef(null);

  useEffect(() => {
    let interval;
    if (chatOpen && pelanggan) {
      loadChat();
      interval = setInterval(loadChat, 5000); // Poll every 5s
    }
    return () => clearInterval(interval);
  }, [chatOpen, pelanggan]);

  useEffect(() => {
    if (messagesEndRef.current) {
      messagesEndRef.current.scrollIntoView({ behavior: 'smooth' });
    }
  }, [chatMessages, chatOpen]);

  const loadChat = async () => {
    if (!pelanggan) return;
    try {
      const res = await api.get(`/chat/pelanggan/${pelanggan.idPelanggan}`);
      if (res.data.success) {
        setChatMessages(res.data.data.messages || []);
      }
    } catch (err) {
      console.error('Gagal memuat chat', err);
    }
  };

  const sendChat = async (e) => {
    e.preventDefault();
    if (!chatInput.trim() || !pelanggan) return;
    const body = chatInput.trim();
    setChatInput('');
    // Optimistic UI update
    setChatMessages(prev => [...prev, { id: Date.now(), senderType: 'pelanggan', body, createdAt: new Date().toISOString() }]);
    try {
      await api.post(`/chat/pelanggan/${pelanggan.idPelanggan}`, { body });
      loadChat();
    } catch (err) {
      console.error('Gagal mengirim pesan', err);
    }
  };

  useEffect(() => {
    const saved = localStorage.getItem('portal_pelanggan_id');
    if (saved) {
      loginPelanggan(saved);
    } else {
      setLoading(false);
    }
  }, []);

  const loginPelanggan = async (idPel) => {
    setLoading(true);
    setErr('');
    try {
      // Use the dedicated client-login endpoint (Public)
      const res = await api.post('/auth/client-login', { pelId: idPel });
      if (!res.data.success) { 
        setErr(res.data.message || 'ID Pelanggan tidak ditemukan.'); 
        setLoading(false); 
        return; 
      }
      
      const pel = res.data.data;
      setPelanggan(pel);
      setIsLoggedIn(true);
      localStorage.setItem('portal_pelanggan_id', idPel);

      // Load tagihan (Public endpoint)
      const now = new Date();
      const tRes = await api.get(`/tagihan/pelanggan/${pel.idPelanggan}?bulan=${now.getMonth() + 1}&tahun=${now.getFullYear()}`);
      const tags = tRes.data.data || [];
      setTagihan(tags[0] || null);

      // Load paket (Public endpoint)
      try {
        const pRes = await api.get('/paket/publik');
        setPaket(pRes.data.data || []);
      } catch { }

      // Load pengumuman (Skip if fails due to auth or add public endpoint)
      try {
        // This might still fail if /collections is protected, we'll fix the backend next
        const aRes = await api.get('/collections/pengumuman?aktif=1');
        setPengumuman(aRes.data.data || aRes.data || []);
      } catch { }

    } catch (err) {
      setErr('ID Pelanggan tidak ditemukan atau gagal terhubung.');
    } finally { setLoading(false); }
  };

  const handleLogin = (e) => {
    e.preventDefault();
    if (loginForm.idPelanggan.trim()) loginPelanggan(loginForm.idPelanggan.trim());
  };

  const handleLogout = () => {
    setIsLoggedIn(false);
    setPelanggan(null);
    setTagihan(null);
    localStorage.removeItem('portal_pelanggan_id');
  };

  if (loading) return <div className="portal-loading"><div className="spinner" /><p>Memuat...</p></div>;

  // LOGIN
  if (!isLoggedIn) {
    return (
      <div className="portal-login-wrapper">
        <div className="portal-login-card">
          <div className="portal-login-logo">⚡</div>
          <h2>Portal Pelanggan</h2>
          <p>Masukkan ID Pelanggan Anda</p>
          <form onSubmit={handleLogin}>
            <input
              className="portal-input"
              placeholder="ID Pelanggan (contoh: 11165)"
              value={loginForm.idPelanggan}
              onChange={(e) => setLoginForm({ idPelanggan: e.target.value })}
              required
            />
            {err && <div className="portal-error">{err}</div>}
            <button type="submit" className="portal-btn-login">Masuk</button>
          </form>
        </div>
      </div>
    );
  }

  // PORTAL
  const now = new Date();
  const initials = (pelanggan?.nama || '?').slice(0, 2).toUpperCase();


  return (
    <div className="portal-container">
      {/* Header */}
      <div className="portal-header">
        <div className="portal-user-info">
          <div className="portal-avatar">{initials}</div>
          <div>
            <h3>{pelanggan?.nama || '-'}</h3>
            <p>ID: {pelanggan?.idPelanggan} • {pelanggan?.area || '-'}</p>
          </div>
        </div>
        <button className="portal-btn-logout" onClick={handleLogout}>Keluar</button>
      </div>

      {/* Pengumuman */}
      {pengumuman.length > 0 && (
        <div className="portal-announcement">
          <span className="portal-announcement-icon">📢</span>
          <div className="portal-marquee">
            <span>{pengumuman.map(p => p.pesan).join(' | ')}</span>
          </div>
        </div>
      )}

      {/* Tagihan Card */}
      <div className="portal-bill-card">
        <div className="portal-bill-header">
          <span className="portal-bill-month">{BULAN_NAMA[now.getMonth()]} {now.getFullYear()}</span>
          <span className={`portal-badge ${tagihan?.status === 'lunas' ? 'portal-badge-success' : 'portal-badge-danger'}`}>
            {tagihan?.status === 'lunas' ? '✅ LUNAS' : '❌ BELUM BAYAR'}
          </span>
        </div>
        <div className="portal-bill-amount">{formatRupiah(tagihan?.totalTagihan || pelanggan?.hargaPaket || 0)}</div>
        <div className="portal-bill-due">Jatuh tempo: <strong>Tgl {pelanggan?.tglTagih || '-'}</strong></div>
        <div className="portal-bill-details">
          <div className="portal-detail-row"><span>Paket</span><strong>{pelanggan?.paket || '-'}</strong></div>
          <div className="portal-detail-row"><span>Status</span><strong>{pelanggan?.status || '-'}</strong></div>
          <div className="portal-detail-row"><span>Alamat</span><strong>{pelanggan?.alamat || '-'}</strong></div>
        </div>
      </div>

      {/* Action Grid */}
      <div className="portal-actions">
        <a className="portal-action-btn portal-btn-pay" href={pelanggan?.noWA ? `https://wa.me/${pelanggan.noWA}` : '#'} target="_blank" rel="noreferrer">
          <span className="portal-action-icon">💳</span>
          <span>Bayar Via WA</span>
        </a>
        <button className="portal-action-btn portal-btn-cs" onClick={() => setChatOpen(true)} style={{ background: 'rgba(0, 0, 0, 0.02)', color: 'var(--text-secondary)', border: '1px solid rgba(0,0,0,0.08)', cursor: 'pointer', fontWeight: 600 }}>
          <span className="portal-action-icon">💬</span>
          <span>Chat Admin</span>
        </button>
      </div>

      {/* Paket */}
      {paket.length > 0 && (
        <div className="portal-section">
          <h3>📦 Paket Tersedia</h3>
          <div className="portal-package-list">
            {paket.map(p => (
              <div key={p.id} className="portal-package-item">
                <div style={{ display: 'flex', justifyContent: 'space-between' }}>
                  <strong>{p.nama || '-'}</strong>
                  <span style={{ color: 'var(--color-success)', fontWeight: 700 }}>{formatRupiah(p.harga)}</span>
                </div>
                <div style={{ fontSize: 12, color: 'var(--text-muted)', marginTop: 4 }}>{p.keterangan || '-'}</div>
              </div>
            ))}
          </div>
        </div>
      )}

      {/* Chat Widget */}
      <div className="portal-chat-widget">
        {!chatOpen && (
          <button className="portal-chat-btn" onClick={() => setChatOpen(true)}>
            💬
          </button>
        )}
        
        {chatOpen && (
          <div className="portal-chat-window">
            <div className="portal-chat-header">
              <span>💬 Chat dengan Admin</span>
              <button onClick={() => setChatOpen(false)} style={{ background: 'none', border: 'none', color: 'white', cursor: 'pointer', fontSize: 18 }}>✕</button>
            </div>
            <div className="portal-chat-messages">
              {chatMessages.length === 0 ? (
                <div style={{ textAlign: 'center', color: 'var(--text-muted)', marginTop: 20, fontSize: 13 }}>
                  Kirim pesan untuk mulai percakapan dengan admin.
                </div>
              ) : (
                chatMessages.map(msg => (
                  <div key={msg.id} className={`portal-chat-msg ${msg.senderType === 'pelanggan' ? 'pelanggan' : 'admin'}`}>
                    {msg.body}
                    <div style={{ fontSize: 10, opacity: 0.7, marginTop: 4, textAlign: msg.senderType === 'pelanggan' ? 'right' : 'left' }}>
                      {new Date(msg.createdAt).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}
                    </div>
                  </div>
                ))
              )}
              <div ref={messagesEndRef} />
            </div>
            <form onSubmit={sendChat} className="portal-chat-input-area">
              <input
                type="text"
                className="portal-chat-input"
                placeholder="Ketik pesan..."
                value={chatInput}
                onChange={e => setChatInput(e.target.value)}
              />
              <button type="submit" className="portal-chat-send" disabled={!chatInput.trim()}>
                ➤
              </button>
            </form>
          </div>
        )}
      </div>

    </div>
  );
}
