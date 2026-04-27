import { useNavigate } from 'react-router-dom';
import useAuthStore from '../../store/useAuthStore';
import { getInitials } from '../../utils/format';

export default function Topbar({ title }) {
  const { user, logout } = useAuthStore();
  const navigate = useNavigate();

  const handleLogout = () => {
    logout();
    navigate('/login');
  };

  return (
    <header className="topbar" id="main-topbar">
      <h2 className="topbar-title">{title || 'Dashboard'}</h2>
      <div className="topbar-right">
        <div
          className="topbar-avatar"
          title={user?.nama || 'User'}
          onClick={handleLogout}
          style={{ cursor: 'pointer' }}
        >
          {getInitials(user?.nama)}
        </div>
      </div>
    </header>
  );
}
