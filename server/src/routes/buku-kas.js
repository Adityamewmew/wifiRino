const express = require('express');
const router = express.Router();
const crypto = require('crypto');
const db = require('../config/database');
const { authenticateToken, canViewFinanceTotals } = require('../middleware/auth');

router.get('/', authenticateToken, async (req, res) => {
  try {
    if (!canViewFinanceTotals(req.user)) return res.status(403).json({ success: false, message: 'Akses ditolak.' });
    const { bulan, tahun, jenis, page = 1, limit = 50 } = req.query;
    let sql = 'SELECT * FROM pembukuan';
    const conditions = [];
    const params = [];
    if (jenis) { conditions.push('jenis = ?'); params.push(jenis); }
    if (tahun && bulan) {
      conditions.push("DATE_FORMAT(tanggal, '%Y-%m') = ?");
      params.push(`${tahun}-${String(bulan).padStart(2, '0')}`);
    } else if (tahun) {
      conditions.push("YEAR(tanggal) = ?");
      params.push(parseInt(tahun));
    }
    if (conditions.length) sql += ' WHERE ' + conditions.join(' AND ');
    sql += ' ORDER BY tanggal DESC';
    const countSql = sql.replace('SELECT *', 'SELECT COUNT(*) as total');
    const [countResult] = await db.query(countSql, params);
    const total = countResult[0].total;
    const offset = (parseInt(page) - 1) * parseInt(limit);
    sql += ` LIMIT ${parseInt(limit)} OFFSET ${offset}`;
    const rows = await db.all(sql, params);
    res.json({ success: true, data: rows, total, page: parseInt(page), limit: parseInt(limit) });
  } catch (err) { res.status(500).json({ success: false, message: err.message }); }
});

router.post('/', authenticateToken, async (req, res) => {
  try {
    if (!canViewFinanceTotals(req.user)) return res.status(403).json({ success: false, message: 'Akses ditolak.' });
    const id = req.body.id || crypto.randomUUID();
    const { tanggal, jenis, kategori, nominal, keterangan } = req.body;
    await db.run(
      'INSERT INTO pembukuan (id, tanggal, jenis, kategori, nominal, keterangan, createdBy, createdAt) VALUES (?,?,?,?,?,?,?,?)',
      [id, tanggal || new Date().toISOString(), jenis, kategori, nominal || 0, keterangan || '', req.user.email, new Date().toISOString()]
    );
    res.json({ success: true, data: { id }, message: 'Transaksi berhasil ditambahkan' });
  } catch (err) { res.status(500).json({ success: false, message: err.message }); }
});

router.delete('/:id', authenticateToken, async (req, res) => {
  try {
    if (!canViewFinanceTotals(req.user)) return res.status(403).json({ success: false, message: 'Akses ditolak.' });
    await db.run('DELETE FROM pembukuan WHERE id = ?', [req.params.id]);
    res.json({ success: true, message: 'Transaksi berhasil dihapus' });
  } catch (err) { res.status(500).json({ success: false, message: err.message }); }
});

module.exports = router;
