import { NavLink } from 'react-router-dom';
import useAuthStore from '../../store/useAuthStore';

const navItems = [
  { label: 'Dashboard', icon: '📊', path: '/' },
  { section: 'MANAJEMEN' },
  { label: 'Pelanggan', icon: '👥', path: '/pelanggan' },
  { label: 'Tagihan', icon: '📄', path: '/tagihan' },
  { label: 'Paket Internet', icon: '📦', path: '/paket' },
  { label: 'Area/Wilayah', icon: '📍', path: '/area' },
  { section: 'KEUANGAN' },
  { label: 'Buku Kas', icon: '💰', path: '/buku-kas' },
  { label: 'Pembukuan Agen', icon: '📋', path: '/pembukuan-agen' },
  { label: 'Pembukuan Saya', icon: '📒', path: '/pembukuan-saya' },
  { label: 'Tagih Pelanggan', icon: '🏠', path: '/tagih-pelanggan' },
  { section: 'OPERASIONAL' },
  { label: 'Tugas Teknisi', icon: '🔧', path: '/tugas-teknisi' },
  { label: 'Gangguan', icon: '⚠️', path: '/troubleshoot' },
  { label: 'Pemasangan Baru', icon: '🔌', path: '/pemasangan' },
  { section: 'KOMUNIKASI' },
  { label: 'Pengumuman', icon: '📢', path: '/pengumuman' },
  { label: 'Messaging', icon: '💬', path: '/messaging' },
  { section: 'SISTEM' },
  { label: 'Pengaturan', icon: '⚙️', path: '/pengaturan' },
];

export default function Sidebar() {
  const { user } = useAuthStore();

  return (
    <aside className="sidebar" id="main-sidebar">
      <div className="sidebar-header">
        <div className="sidebar-logo">⚡</div>
        <span className="sidebar-brand">RinoNet</span>
      </div>
      <nav className="sidebar-nav">
        {navItems.map((item, i) => {
          if (item.section) {
            return <div key={i} className="nav-section-label">{item.section}</div>;
          }
          return (
            <NavLink
              key={item.path}
              to={item.path}
              className={({ isActive }) => `nav-item${isActive ? ' active' : ''}`}
              end={item.path === '/'}
            >
              <span className="nav-item-icon">{item.icon}</span>
              <span>{item.label}</span>
            </NavLink>
          );
        })}
      </nav>
      {user && (
        <div style={{
          padding: '16px 20px',
          borderTop: '1px solid rgba(255,255,255,0.1)',
          color: 'rgba(255,255,255,0.6)',
          fontSize: '12px',
        }}>
          <div style={{ color: 'white', fontWeight: 600, marginBottom: 2 }}>{user.nama}</div>
          <div>{user.email}</div>
        </div>
      )}
    </aside>
  );
}
