const express = require('express');
const router = express.Router();
const crypto = require('crypto');
const db = require('../config/database');
const { authenticateToken } = require('../middleware/auth');

router.get('/', authenticateToken, async (req, res) => {
  try {
    const rows = await db.all('SELECT * FROM paket ORDER BY harga ASC, nama ASC');
    res.json({ success: true, data: rows });
  } catch (err) { res.status(500).json({ success: false, message: err.message }); }
});

router.get('/publik', async (req, res) => {
  try {
    const rows = await db.all("SELECT id, nama, harga, deskripsi, aktif FROM paket WHERE COALESCE(aktif,1) = 1 ORDER BY harga ASC");
    res.json({ success: true, data: rows });
  } catch (err) { res.status(500).json({ success: false, message: err.message }); }
});

router.post('/', authenticateToken, async (req, res) => {
  try {
    const id = req.body.id || crypto.randomUUID();
    const { nama, harga, deskripsi, aktif = 1 } = req.body;
    await db.run('INSERT INTO paket (id, nama, harga, deskripsi, aktif) VALUES (?,?,?,?,?)', [id, nama, harga || 0, deskripsi || '', aktif]);
    res.json({ success: true, data: { id }, message: 'Paket berhasil ditambahkan' });
  } catch (err) { res.status(500).json({ success: false, message: err.message }); }
});

router.put('/:id', authenticateToken, async (req, res) => {
  try {
    const { nama, harga, deskripsi, aktif } = req.body;
    await db.run('UPDATE paket SET nama=?, harga=?, deskripsi=?, aktif=? WHERE id=?', [nama, harga||0, deskripsi||'', aktif??1, req.params.id]);
    res.json({ success: true, message: 'Paket berhasil diperbarui' });
  } catch (err) { res.status(500).json({ success: false, message: err.message }); }
});

router.delete('/:id', authenticateToken, async (req, res) => {
  try {
    await db.run('DELETE FROM paket WHERE id = ?', [req.params.id]);
    res.json({ success: true, message: 'Paket berhasil dihapus' });
  } catch (err) { res.status(500).json({ success: false, message: err.message }); }
});

module.exports = router;
