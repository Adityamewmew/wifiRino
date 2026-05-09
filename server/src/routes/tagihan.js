/**
 * Tagihan Routes — CRUD tagihan, sync, bayar, struk
 */
const express = require('express');
const router = express.Router();
const crypto = require('crypto');
const db = require('../config/database');
const { authenticateToken, canCollectPayment, isAdminRole, resolveUserRoleKey } = require('../middleware/auth');

// GET /api/tagihan — semua tagihan (with filters)
router.get('/', authenticateToken, async (req, res) => {
  try {
    const { bulan, tahun, status, area, search, page = 1, limit = 50 } = req.query;
    let sql = 'SELECT * FROM tagihan_bulanan';
    const conditions = [];
    const params = [];

    if (bulan) { conditions.push('bulan = ?'); params.push(parseInt(bulan)); }
    if (tahun) { conditions.push('tahun = ?'); params.push(parseInt(tahun)); }
    if (status) { conditions.push('LOWER(status) = LOWER(?)'); params.push(status); }
    if (area) { conditions.push('area = ?'); params.push(area); }
    if (search) {
      conditions.push('(namaPelanggan LIKE ? OR idPelanggan LIKE ?)');
      params.push(`%${search}%`, `%${search}%`);
    }

    if (conditions.length) sql += ' WHERE ' + conditions.join(' AND ');
    sql += ' ORDER BY tahun DESC, bulan DESC, namaPelanggan ASC';

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

// GET /api/tagihan/sync — auto-generate tagihan for bulan/tahun
router.get('/sync', authenticateToken, async (req, res) => {
  try {
    const { bulan, tahun } = req.query;
    if (!bulan || !tahun) return res.status(400).json({ success: false, message: "Bulan dan tahun diperlukan" });

    const blnInt = parseInt(bulan);
    const thnInt = parseInt(tahun);
    const selectedDate = new Date(thnInt, blnInt - 1, 1);

    const pelangganArr = await db.all("SELECT * FROM pelanggan WHERE status = 'aktif'");
    const existingRows = await db.all("SELECT idPelanggan FROM tagihan_bulanan WHERE bulan = ? AND tahun = ?", [blnInt, thnInt]);
    const existingSet = new Set(existingRows.map(r => String(r.idPelanggan || '').trim()).filter(Boolean));
    let countBaru = 0;

    for (const pData of pelangganArr) {
      let mulaiDate = null;
      if (pData.mulaiTagihan) {
        const d = new Date(pData.mulaiTagihan);
        if (!isNaN(d.getTime())) mulaiDate = new Date(d.getFullYear(), d.getMonth(), 1);
      }
      if (!mulaiDate && pData.tanggalMulaiStr) {
        const d = new Date(pData.tanggalMulaiStr);
        if (!isNaN(d.getTime())) mulaiDate = new Date(d.getFullYear(), d.getMonth() + 1, 1);
      }
      if (!mulaiDate && pData.bulanMulai && pData.tahunMulai) {
        mulaiDate = new Date(parseInt(pData.tahunMulai), parseInt(pData.bulanMulai) - 1, 1);
      }
      if (mulaiDate && mulaiDate > selectedDate) continue;

      const pelId = pData.idPelanggan || pData.id;
      if (!pelId || existingSet.has(String(pelId).trim())) continue;

      const tglTagih = pData.tglTagih ? parseInt(pData.tglTagih) : 10;
      const jatuhTempo = new Date(thnInt, blnInt - 1, tglTagih, 23, 59, 59);
      const biaya = pData.totalFinal || pData.hargaPaket || 150000;

      const newId = crypto.randomUUID();
      await db.run(
        `INSERT INTO tagihan_bulanan (id, idPelanggan, namaPelanggan, area, paket, noWA, bulan, tahun, totalTagihan, status, tglJatuhTempo, createdAt) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)`,
        [newId, pelId, pData.nama || '-', pData.area || 'Unknown', pData.paket || '-', pData.noWA || '', blnInt, thnInt, biaya, 'belum', jatuhTempo.toISOString(), new Date().toISOString()]
      );
      countBaru++;
      existingSet.add(String(pelId).trim());
    }

    res.json({ success: true, data: { generated: countBaru } });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// GET /api/tagihan/pelanggan/:idPelanggan — public endpoint
router.get('/pelanggan/:idPelanggan', async (req, res) => {
  try {
    const { idPelanggan } = req.params;
    const { bulan, tahun } = req.query;
    let sql = "SELECT * FROM tagihan_bulanan WHERE idPelanggan = ?";
    const params = [idPelanggan];
    if (bulan) { sql += " AND bulan = ?"; params.push(parseInt(bulan)); }
    if (tahun) { sql += " AND tahun = ?"; params.push(parseInt(tahun)); }
    sql += " ORDER BY tahun DESC, bulan DESC LIMIT 12";
    const rows = await db.all(sql, params);
    res.json({ success: true, data: rows });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// GET /api/tagihan/:id/struk — data struk untuk print
router.get('/:id/struk', authenticateToken, async (req, res) => {
  try {
    const row = await db.get("SELECT * FROM tagihan_bulanan WHERE id = ?", [req.params.id]);
    if (!row) return res.status(404).json({ success: false, message: "Tagihan tidak ditemukan" });
    // Get pengaturan for struk header
    const settings = await db.all("SELECT * FROM pengaturan");
    const settingsMap = {};
    settings.forEach(s => { settingsMap[s.kunci] = s.nilai; });
    res.json({ success: true, data: { tagihan: row, pengaturan: settingsMap } });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// POST /api/tagihan/:id/bayar — konfirmasi bayar
router.post('/:id/bayar', authenticateToken, async (req, res) => {
  try {
    if (!canCollectPayment(req.user)) {
      return res.status(403).json({ success: false, message: 'Akses ditolak.' });
    }
    const row = await db.get("SELECT * FROM tagihan_bulanan WHERE id = ?", [req.params.id]);
    if (!row) return res.status(404).json({ success: false, message: "Tagihan tidak ditemukan" });
    if (row.status === 'lunas') return res.status(400).json({ success: false, message: "Tagihan sudah lunas" });

    const userRow = await db.get("SELECT nama FROM users WHERE id = ?", [req.user.uid]);
    const collectorName = String(req.body?.dibayar_ke || userRow?.nama || req.user.email || '').trim();
    const nowIso = new Date().toISOString();

    // Update tagihan
    await db.run("UPDATE tagihan_bulanan SET status = 'lunas', tglBayar = ?, metodeBayar = 'cash', dibayar_ke = ? WHERE id = ?", [nowIso, collectorName, req.params.id]);

    // Insert pembukuan
    const pembukuanId = crypto.randomUUID();
    await db.run(
      `INSERT INTO pembukuan (id, tanggal, jenis, kategori, nominal, keterangan, createdBy, idReferensi, createdAt) VALUES (?,?,?,?,?,?,?,?,?)`,
      [pembukuanId, nowIso, 'pemasukan', 'Tagihan Internet', row.totalTagihan || 0,
       `Pemb. a.n ${row.namaPelanggan} (ID: ${row.idPelanggan}) - Bln ${row.bulan}/${row.tahun}`,
       req.user.email, req.params.id, nowIso]
    );

    res.json({ success: true, message: 'Tagihan berhasil dibayar' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// POST /api/tagihan/:id/undo — batalkan bayar
router.post('/:id/undo', authenticateToken, async (req, res) => {
  try {
    if (!isAdminRole(req.user.role)) return res.status(403).json({ success: false, message: 'Akses ditolak.' });
    await db.run("UPDATE tagihan_bulanan SET status = 'belum', tglBayar = NULL, metodeBayar = NULL WHERE id = ?", [req.params.id]);
    await db.run("DELETE FROM pembukuan WHERE idReferensi = ?", [req.params.id]);
    res.json({ success: true, message: 'Pembayaran berhasil dibatalkan' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

module.exports = router;
