/**
 * Pelanggan Routes — CRUD pelanggan
 */
const express = require('express');
const router = express.Router();
const crypto = require('crypto');
const db = require('../config/database');
const { authenticateToken, isAdminRole } = require('../middleware/auth');

// GET /api/pelanggan — semua pelanggan
router.get('/', authenticateToken, async (req, res) => {
  try {
    const { search, status, area, page = 1, limit = 50 } = req.query;
    let sql = 'SELECT * FROM pelanggan';
    const conditions = [];
    const params = [];

    if (status) { conditions.push('status = ?'); params.push(status); }
    if (area) { conditions.push('area = ?'); params.push(area); }
    if (search) {
      conditions.push('(nama LIKE ? OR idPelanggan LIKE ? OR noWA LIKE ?)');
      params.push(`%${search}%`, `%${search}%`, `%${search}%`);
    }

    if (conditions.length) sql += ' WHERE ' + conditions.join(' AND ');
    sql += ' ORDER BY nama ASC';

    const countSql = sql.replace('SELECT *', 'SELECT COUNT(*) as total');
    const [countResult] = await db.query(countSql, params);
    const total = countResult[0].total;

    const offset = (parseInt(page) - 1) * parseInt(limit);
    sql += ` LIMIT ${parseInt(limit)} OFFSET ${offset}`;

    const rows = await db.all(sql, params);
    res.json({ success: true, data: rows, total, page: parseInt(page), limit: parseInt(limit) });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// GET /api/pelanggan/:id
router.get('/:id', authenticateToken, async (req, res) => {
  try {
    const row = await db.get('SELECT * FROM pelanggan WHERE id = ? OR idPelanggan = ?', [req.params.id, req.params.id]);
    if (!row) return res.status(404).json({ success: false, message: 'Pelanggan tidak ditemukan' });
    res.json({ success: true, data: row });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// POST /api/pelanggan
router.post('/', authenticateToken, async (req, res) => {
  try {
    const data = { ...req.body, id: req.body.id || crypto.randomUUID(), createdAt: new Date().toISOString() };
    // Serialize nested objects (biayaTambahan, diskon)
    if (typeof data.biayaTambahan1 === 'object') data.biayaTambahan1 = JSON.stringify(data.biayaTambahan1);
    if (typeof data.biayaTambahan2 === 'object') data.biayaTambahan2 = JSON.stringify(data.biayaTambahan2);
    if (typeof data.diskon === 'object') data.diskon = JSON.stringify(data.diskon);

    const keys = Object.keys(data);
    const values = Object.values(data);
    const placeholders = keys.map(() => '?').join(', ');

    await db.run(`INSERT INTO pelanggan (${keys.join(', ')}) VALUES (${placeholders})`, values);
    res.json({ success: true, data: { id: data.id }, message: 'Pelanggan berhasil ditambahkan' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// PUT /api/pelanggan/:id
router.put('/:id', authenticateToken, async (req, res) => {
  try {
    const data = { ...req.body, updatedAt: new Date().toISOString() };
    delete data.id;
    if (typeof data.biayaTambahan1 === 'object') data.biayaTambahan1 = JSON.stringify(data.biayaTambahan1);
    if (typeof data.biayaTambahan2 === 'object') data.biayaTambahan2 = JSON.stringify(data.biayaTambahan2);
    if (typeof data.diskon === 'object') data.diskon = JSON.stringify(data.diskon);

    const keys = Object.keys(data);
    const values = Object.values(data);
    const setClause = keys.map(k => `${k} = ?`).join(', ');
    values.push(req.params.id);

    await db.run(`UPDATE pelanggan SET ${setClause} WHERE id = ?`, values);
    res.json({ success: true, message: 'Pelanggan berhasil diperbarui' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// DELETE /api/pelanggan/:id
router.delete('/:id', authenticateToken, async (req, res) => {
  try {
    await db.run('DELETE FROM pelanggan WHERE id = ?', [req.params.id]);
    res.json({ success: true, message: 'Pelanggan berhasil dihapus' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

module.exports = router;
