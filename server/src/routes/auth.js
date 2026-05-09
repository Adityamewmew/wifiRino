/**
 * Auth Routes — migrated from server.js
 * Logika tidak berubah.
 */
const express = require('express');
const router = express.Router();
const jwt = require('jsonwebtoken');
const bcrypt = require('bcryptjs');
const db = require('../config/database');
const {
  JWT_SECRET, authenticateToken, buildAuthUserProfile,
  resolveRoleKey, roleToCompat, getPermissionsByRole,
  isAdminRole,
} = require('../middleware/auth');

// Parse user.areas helper
function extractAreaRefs(raw) {
  if (raw === null || raw === undefined) return [];
  if (Array.isArray(raw)) return raw.flatMap(extractAreaRefs);
  if (typeof raw === 'object') return [raw.id, raw.nama, raw.area, raw.value].flatMap(extractAreaRefs);
  const txt = String(raw || '').trim();
  if (!txt) return [];
  if ((txt.startsWith('[') && txt.endsWith(']')) || (txt.startsWith('{') && txt.endsWith('}'))) {
    try { return extractAreaRefs(JSON.parse(txt)); } catch { }
  }
  if (txt.includes(',')) return txt.split(',').flatMap(extractAreaRefs);
  return [txt];
}

function parseUserAreas(val) {
  return [...new Set(
    extractAreaRefs(val)
      .map(v => String(v || '').trim())
      .filter(Boolean)
      .filter(v => v.toLowerCase() !== '[object object]')
  )];
}

async function resolveUserAreas(user) {
  const parsed = parseUserAreas(user?.areas);
  if (parsed.length > 0) return parsed;
  const rawAreas = String(user?.areas || '').toLowerCase();
  const role = String(user?.role || '').toLowerCase();
  const isCorruptLegacy = rawAreas.includes('[object object]');
  const isAdmin = isAdminRole(role);
  if (!isCorruptLegacy || isAdmin) return parsed;
  try {
    const areaRows = await db.all("SELECT id, nama FROM areas");
    const areaByName = new Map(areaRows.map(a => [String(a.nama || '').trim().toLowerCase(), String(a.id || '').trim()]));
    const distinctAreas = await db.all("SELECT DISTINCT area FROM pelanggan WHERE area IS NOT NULL AND TRIM(area) <> ''");
    if (distinctAreas.length === 1) {
      const onlyAreaName = String(distinctAreas[0].area || '').trim();
      const onlyAreaId = areaByName.get(onlyAreaName.toLowerCase()) || onlyAreaName;
      if (onlyAreaId && user?.id) {
        const fixed = [onlyAreaId];
        await db.run("UPDATE users SET areas = ? WHERE id = ?", [JSON.stringify(fixed), user.id]);
        return fixed;
      }
    }
  } catch (err) {
    console.warn('Gagal auto-repair areas user:', err.message);
  }
  return parsed;
}

// POST /api/auth/login
router.post('/login', async (req, res) => {
  const { email, password } = req.body;
  try {
    const user = await db.get('SELECT * FROM users WHERE email = ?', [email]);
    if (!user) return res.status(401).json({ success: false, message: 'User tidak ditemukan' });
    if (Number(user.aktif) === 0) return res.status(403).json({ success: false, message: 'Akun dinonaktifkan. Hubungi administrator.' });

    const match = await bcrypt.compare(password, user.password);
    if (!match) return res.status(401).json({ success: false, message: 'Password salah' });

    const resolvedAreas = await resolveUserAreas(user);
    const authUser = buildAuthUserProfile(user, resolvedAreas);
    const token = jwt.sign({
      uid: authUser.uid, email: authUser.email,
      role: authUser.role, roleKey: authUser.roleKey,
      permissions: authUser.permissions,
    }, JWT_SECRET);
    res.json({ success: true, data: { token, user: authUser } });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// POST /api/auth/client-login
router.post('/client-login', async (req, res) => {
  const { pelId } = req.body;
  try {
    if (!pelId) return res.status(400).json({ success: false, message: 'ID atau No WA diperlukan' });
    const inputId = pelId.trim().toLowerCase();
    let inputWa = inputId.replace(/\D/g, '');
    if (inputWa.startsWith('0')) inputWa = '62' + inputWa.substring(1);

    const pelangganArr = await db.all("SELECT * FROM pelanggan");
    const matchRow = pelangganArr.find(row => {
      const id = (row.idPelanggan || row.id || '').toLowerCase();
      let pWa = (row.noWA || '').replace(/\D/g, '');
      if (pWa.startsWith('0')) pWa = '62' + pWa.substring(1);
      return id === inputId || (inputWa.length >= 10 && pWa === inputWa);
    });

    if (!matchRow) return res.status(404).json({ success: false, message: 'ID Pelanggan atau Nomor WA tidak ditemukan' });

    res.json({
      success: true,
      data: {
        id: matchRow.id,
        idPelanggan: matchRow.idPelanggan || matchRow.id,
        nama: matchRow.nama || 'Pelanggan',
        area: matchRow.area,
        paket: matchRow.paket,
        noWA: matchRow.noWA,
        status: matchRow.status || 'aktif',
      }
    });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// GET /api/auth/me
router.get('/me', authenticateToken, async (req, res) => {
  try {
    const user = await db.get('SELECT id, nama, email, role, aktif, areas FROM users WHERE id = ?', [req.user.uid]);
    if (!user) return res.sendStatus(404);
    const resolvedAreas = await resolveUserAreas(user);
    res.json({ success: true, data: buildAuthUserProfile(user, resolvedAreas) });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// POST /api/auth/verify-password
router.post('/verify-password', authenticateToken, async (req, res) => {
  try {
    const password = String(req.body?.password || '').trim();
    if (!password) return res.status(400).json({ success: false, message: 'Password wajib diisi' });
    const user = await db.get('SELECT password FROM users WHERE id = ?', [req.user.uid]);
    if (!user || !user.password) return res.status(404).json({ success: false, message: 'User tidak ditemukan' });
    const match = await bcrypt.compare(password, user.password);
    if (!match) return res.status(401).json({ success: false, message: 'Password salah' });
    return res.json({ success: true });
  } catch (err) {
    return res.status(500).json({ success: false, message: err.message });
  }
});

module.exports = router;
