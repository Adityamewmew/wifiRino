import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import useAuthStore from './store/useAuthStore';
import AppLayout from './components/layout/AppLayout';
import LoginPage from './pages/Login';
import DashboardPage from './pages/Dashboard';
import PelangganPage from './pages/Pelanggan';
import TagihanPage from './pages/Tagihan';
import PaketPage from './pages/Paket';
import BukuKasPage from './pages/BukuKas';
import PembukuanAgenPage from './pages/PembukuanAgen';
import TugasTeknisiPage from './pages/TugasTeknisi';
import PemasanganPage from './pages/Pemasangan';
import PengaturanPage from './pages/Pengaturan';
import AreaPage from './pages/Area';
import PengumumanPage from './pages/Pengumuman';
import TroubleshootPage from './pages/Troubleshoot';
import PembukuanSayaPage from './pages/PembukuanSaya';
import TagihPelangganPage from './pages/TagihPelanggan';
import StrukPage from './pages/Struk';
import MessagingPage from './pages/Messaging';
import PortalPelangganPage from './pages/PortalPelanggan';

function ProtectedRoute({ children }) {
  const { isAuthenticated } = useAuthStore();
  return isAuthenticated ? children : <Navigate to="/login" replace />;
}

function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/login" element={<LoginPage />} />
        {/* Portal Pelanggan (public, no admin layout) */}
        <Route path="/portal" element={<PortalPelangganPage />} />
        <Route
          path="/"
          element={
            <ProtectedRoute>
              <AppLayout />
            </ProtectedRoute>
          }
        >
          <Route index element={<DashboardPage />} />
          <Route path="pelanggan" element={<PelangganPage />} />
          <Route path="tagihan" element={<TagihanPage />} />
          <Route path="paket" element={<PaketPage />} />
          <Route path="buku-kas" element={<BukuKasPage />} />
          <Route path="pembukuan-agen" element={<PembukuanAgenPage />} />
          <Route path="pembukuan-saya" element={<PembukuanSayaPage />} />
          <Route path="tugas-teknisi" element={<TugasTeknisiPage />} />
          <Route path="troubleshoot" element={<TroubleshootPage />} />
          <Route path="pemasangan" element={<PemasanganPage />} />
          <Route path="tagih-pelanggan" element={<TagihPelangganPage />} />
          <Route path="area" element={<AreaPage />} />
          <Route path="pengumuman" element={<PengumumanPage />} />
          <Route path="messaging" element={<MessagingPage />} />
          <Route path="pengaturan" element={<PengaturanPage />} />
          <Route path="struk" element={<StrukPage />} />
        </Route>
        <Route path="*" element={<Navigate to="/" replace />} />
      </Routes>
    </BrowserRouter>
  );
}

export default App;
