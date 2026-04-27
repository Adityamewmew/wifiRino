const express = require('express');
const router = express.Router();
const crypto = require('crypto');
const db = require('../config/database');
const { authenticateToken } = require('../middleware/auth');

router.get('/', authenticateToken, async (req, res) => {
  try {
    const { status } = req.query;
    let sql = 'SELECT * FROM tugas_teknisi';
    const params = [];
    if (status) { sql += ' WHERE status = ?'; params.push(status); }
    sql += ' ORDER BY createdAt DESC';
    const rows = await db.all(sql, params);
    res.json({ success: true, data: rows });
  } catch (err) { res.status(500).json({ success: false, message: err.message }); }
});

router.post('/', authenticateToken, async (req, res) => {
  try {
    const id = req.body.id || crypto.randomUUID();
    const data = { ...req.body, id, createdBy: req.user.email, createdAt: new Date().toISOString(), tglDibuat: new Date().toISOString() };
    const keys = Object.keys(data);
    const values = Object.values(data);
    await db.run(`INSERT INTO tugas_teknisi (${keys.join(',')}) VALUES (${keys.map(()=>'?').join(',')})`, values);
    res.json({ success: true, data: { id }, message: 'Tugas berhasil dibuat' });
  } catch (err) { res.status(500).json({ success: false, message: err.message }); }
});

router.put('/:id', authenticateToken, async (req, res) => {
  try {
    const data = { ...req.body };
    delete data.id;
    const keys = Object.keys(data);
    const values = Object.values(data);
    values.push(req.params.id);
    await db.run(`UPDATE tugas_teknisi SET ${keys.map(k=>k+'=?').join(',')} WHERE id=?`, values);
    res.json({ success: true, message: 'Tugas berhasil diperbarui' });
  } catch (err) { res.status(500).json({ success: false, message: err.message }); }
});

router.delete('/:id', authenticateToken, async (req, res) => {
  try {
    await db.run('DELETE FROM tugas_teknisi WHERE id = ?', [req.params.id]);
    res.json({ success: true, message: 'Tugas berhasil dihapus' });
  } catch (err) { res.status(500).json({ success: false, message: err.message }); }
});

module.exports = router;
