const express = require('express');
const router = express.Router();
const db = require('../config/database');
const { authenticateToken, canViewFinanceTotals } = require('../middleware/auth');

router.get('/keuangan', authenticateToken, async (req, res) => {
  try {
    if (!canViewFinanceTotals(req.user)) return res.status(403).json({ success: false, message: 'Akses ditolak.' });
    const now = new Date();
    const bulan = parseInt(req.query.bulan || (now.getMonth() + 1));
    const tahun = parseInt(req.query.tahun || now.getFullYear());

    const [totalPelanggan] = await db.query("SELECT COUNT(*) as c FROM pelanggan WHERE status = 'aktif'");
    const [totalTagihan] = await db.query("SELECT COUNT(*) as c FROM tagihan_bulanan WHERE bulan=? AND tahun=?", [bulan, tahun]);
    const [lunas] = await db.query("SELECT COUNT(*) as c FROM tagihan_bulanan WHERE bulan=? AND tahun=? AND LOWER(status)='lunas'", [bulan, tahun]);
    const [belum] = await db.query("SELECT COUNT(*) as c FROM tagihan_bulanan WHERE bulan=? AND tahun=? AND LOWER(status)<>'lunas'", [bulan, tahun]);
    const [pemasukan] = await db.query("SELECT COALESCE(SUM(nominal),0) as total FROM pembukuan WHERE jenis='pemasukan' AND DATE_FORMAT(tanggal,'%Y-%m')=?", [`${tahun}-${String(bulan).padStart(2,'0')}`]);
    const [pengeluaran] = await db.query("SELECT COALESCE(SUM(nominal),0) as total FROM pembukuan WHERE jenis='pengeluaran' AND DATE_FORMAT(tanggal,'%Y-%m')=?", [`${tahun}-${String(bulan).padStart(2,'0')}`]);

    res.json({
      success: true,
      data: {
        totalPelangganAktif: totalPelanggan[0].c,
        totalTagihan: totalTagihan[0].c,
        lunas: lunas[0].c,
        belumBayar: belum[0].c,
        pemasukan: pemasukan[0].total,
        pengeluaran: pengeluaran[0].total,
        saldo: pemasukan[0].total - pengeluaran[0].total,
        bulan, tahun
      }
    });
  } catch (err) { res.status(500).json({ success: false, message: err.message }); }
});

router.get('/revenue-trend', authenticateToken, async (req, res) => {
  try {
    const tahun = parseInt(req.query.tahun || new Date().getFullYear());
    const rows = await db.all(
      "SELECT bulan, COALESCE(SUM(totalTagihan),0) as total, COUNT(*) as count FROM tagihan_bulanan WHERE tahun=? AND LOWER(status)='lunas' GROUP BY bulan ORDER BY bulan",
      [tahun]
    );
    res.json({ success: true, data: rows });
  } catch (err) { res.status(500).json({ success: false, message: err.message }); }
});

router.get('/pembukuan/agen-summary', authenticateToken, async (req, res) => {
  try {
    const now = new Date();
    const bulan = parseInt(req.query.bulan || (now.getMonth() + 1));
    const tahun = parseInt(req.query.tahun || now.getFullYear());

    // Get all paid invoices for this month
    const tagihanRows = await db.all(
      "SELECT namaPelanggan, dibayar_ke, totalTagihan FROM tagihan_bulanan WHERE bulan=? AND tahun=? AND LOWER(status)='lunas'",
      [bulan, tahun]
    );

    const summaryMap = new Map();
    const ensureCollector = (name) => {
      const safeName = String(name || '').trim() || 'Unknown';
      const key = safeName.toLowerCase();
      if (!summaryMap.has(key)) {
        summaryMap.set(key, { namaPenagih: safeName, totalTagihan: 0, jumlahPelanggan: 0, setor: 0, pengeluaran: 0, sisaDiTangan: 0 });
      }
      return summaryMap.get(key);
    };

    tagihanRows.forEach(row => {
      const collectorName = String(row.dibayar_ke || '').trim();
      if (!collectorName) return;
      const entry = ensureCollector(collectorName);
      entry.totalTagihan += parseFloat(row.totalTagihan || 0);
      entry.jumlahPelanggan += 1;
    });

    summaryMap.forEach(entry => {
      entry.sisaDiTangan = entry.totalTagihan - entry.setor - entry.pengeluaran;
    });

    const rows = Array.from(summaryMap.values()).sort((a, b) => b.totalTagihan - a.totalTagihan);
    const totals = rows.reduce((acc, r) => {
      acc.totalTagihan += r.totalTagihan;
      acc.totalSetor += r.setor;
      acc.totalSisa += r.sisaDiTangan;
      acc.totalPelanggan += r.jumlahPelanggan;
      return acc;
    }, { totalTagihan: 0, totalSetor: 0, totalSisa: 0, totalPelanggan: 0 });

    res.json({ success: true, data: rows, totals, bulan, tahun });
  } catch (err) { res.status(500).json({ success: false, message: err.message }); }
});

module.exports = router;
