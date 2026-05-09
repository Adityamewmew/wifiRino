const express = require('express');
const router = express.Router();
const db = require('../config/database');
const { authenticateToken, canManageSettings } = require('../middleware/auth');

router.get('/', async (req, res) => {
  try {
    const rows = await db.all('SELECT * FROM pengaturan');
    const map = {};
    rows.forEach(r => { map[r.kunci] = r.nilai; });
    res.json({ success: true, data: map });
  } catch (err) { res.status(500).json({ success: false, message: err.message }); }
});

router.post('/', authenticateToken, async (req, res) => {
  try {
    if (!canManageSettings(req.user)) return res.status(403).json({ success: false, message: 'Akses ditolak.' });
    const entries = Object.entries(req.body || {});
    for (const [kunci, nilai] of entries) {
      await db.run('INSERT INTO pengaturan (kunci, nilai) VALUES (?, ?) ON DUPLICATE KEY UPDATE nilai = ?', [kunci, nilai, nilai]);
    }
    res.json({ success: true, message: 'Pengaturan berhasil disimpan' });
  } catch (err) { res.status(500).json({ success: false, message: err.message }); }
});

module.exports = router;
