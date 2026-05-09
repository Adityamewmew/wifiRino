/**
 * Express Application Setup
 * Migrated from monolithic server.js → modular structure
 */
require('dotenv').config({ path: require('path').join(__dirname, '../.env') });

const express = require('express');
const cors = require('cors');
const path = require('path');
const os = require('os');
const { initializeDatabase } = require('./config/init-db');
const errorHandler = require('./middleware/errorHandler');

// Route imports
const authRoutes = require('./routes/auth');
const pelangganRoutes = require('./routes/pelanggan');
const tagihanRoutes = require('./routes/tagihan');
const paketRoutes = require('./routes/paket');
const bukuKasRoutes = require('./routes/buku-kas');
const pengaturanRoutes = require('./routes/pengaturan');
const teknisiRoutes = require('./routes/teknisi');
const collectionsRoutes = require('./routes/collections');
const statsRoutes = require('./routes/stats');

const app = express();
const PORT = process.env.PORT || 5858;

// Middleware
app.use(cors({
  origin: process.env.CLIENT_URL || 'http://localhost:5173',
  credentials: true,
}));
app.use(express.json({ limit: '10mb' }));

// API Routes
app.use('/api/auth', authRoutes);
app.use('/api/pelanggan', pelangganRoutes);
app.use('/api/tagihan', tagihanRoutes);
app.use('/api/paket', paketRoutes);
app.use('/api/buku-kas', bukuKasRoutes);
app.use('/api/pengaturan', pengaturanRoutes);
app.use('/api/tugas', teknisiRoutes);
app.use('/api/collections', collectionsRoutes);
app.use('/api/stats', statsRoutes);

// Dedicated route for public customer portal chat
const chatRoutes = require('./routes/chat');
app.use('/api/chat', chatRoutes);

// Alias: /api/pembukuan/* → /api/stats/pembukuan/*
app.use('/api/pembukuan', (req, res, next) => {
  req.url = '/pembukuan' + req.url;
  statsRoutes(req, res, next);
});

// Error handler
app.use(errorHandler);

// Start server
async function startServer() {
  try {
    await initializeDatabase();
    app.listen(PORT, '0.0.0.0', () => {
      console.log(`\n=================================================`);
      console.log(`🚀 RinoNet Billing Backend running on port ${PORT}`);
      console.log(`👉 Komputer ini: http://localhost:${PORT}`);
      _logLanAccessUrls(PORT);
      console.log(`=================================================\n`);
    });
  } catch (err) {
    console.error('❌ Failed to start server:', err.message);
    process.exit(1);
  }
}

function _logLanAccessUrls(port) {
  try {
    const ifaces = os.networkInterfaces();
    const lines = [];
    for (const name of Object.keys(ifaces)) {
      for (const iface of ifaces[name] || []) {
        if ((iface.family === 'IPv4' || iface.family === 4) && !iface.internal) {
          lines.push(`   http://${iface.address}:${port}`);
        }
      }
    }
    if (lines.length) {
      console.log(`🌐 Akses dari perangkat lain (LAN):`);
      lines.forEach(l => console.log(l));
    }
  } catch (e) {
    console.warn('Gagal membaca antarmuka jaringan:', e.message);
  }
}

startServer();
