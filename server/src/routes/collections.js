const express = require('express');
const router = express.Router();
const crypto = require('crypto');
const db = require('../config/database');
const { authenticateToken, canViewFinanceTotals, hasPermission, isOwnerUser } = require('../middleware/auth');

const TABLE_MAP = {
  pelanggan: 'pelanggan', tagihan: 'tagihan_bulanan', tagihan_bulanan: 'tagihan_bulanan',
  paket: 'paket', areas: 'areas', pembukuan: 'pembukuan', users: 'users',
  tugas_teknisi: 'tugas_teknisi', pengumuman: 'pengumuman', audit_logs: 'audit_logs',
  chat_threads: 'chat_threads', chat_messages: 'chat_messages',
};

function getTable(collection) { return TABLE_MAP[collection] || null; }

// Public route for pengumuman (used by customer portal)
router.get('/pengumuman', async (req, res, next) => {
  // If it's specifically for pengumuman, we allow it without auth
  try {
    const rows = await db.all(`SELECT * FROM pengumuman WHERE aktif = 1`);
    res.json({ success: true, data: rows });
  } catch (err) { res.status(500).json({ success: false, message: err.message }); }
});

router.get('/:collection', authenticateToken, async (req, res) => {
  const table = getTable(req.params.collection);
  if (!table) return res.status(400).json({ success: false, message: 'Invalid collection' });
  try {
    let sql = `SELECT * FROM ${table}`;
    let params = [];
    const conditions = [];

    // Custom logic for chat_threads to include pelanggan data
    if (table === 'chat_threads') {
      sql = `SELECT t.*, p.nama as pelangganNama 
             FROM chat_threads t 
             LEFT JOIN pelanggan p ON t.idPelanggan = p.idPelanggan OR t.pelangganDbId = p.id`;
    } else if (table === 'chat_messages') {
      // Include thread info if needed, or just plain
    }

    // Handle query filters
    for (const [k, v] of Object.entries(req.query)) {
      if (k !== 'limit' && k !== 'offset' && k !== 'sort') {
        conditions.push(`${table === 'chat_threads' ? 't.' : ''}${k} = ?`);
        params.push(v);
      }
    }

    if (conditions.length > 0) {
      sql += ` WHERE ${conditions.join(' AND ')}`;
    }

    // Add order by for chat_messages
    if (table === 'chat_messages') {
      sql += ` ORDER BY createdAt ASC`;
    } else if (table === 'chat_threads') {
      sql += ` ORDER BY t.lastMessageAt DESC`;
    }

    const rows = await db.all(sql, params);

    // If it's chat_threads, let's attach the last message body as well
    if (table === 'chat_threads') {
      for (const t of rows) {
        const lastMsg = await db.get('SELECT body FROM chat_messages WHERE threadId = ? ORDER BY createdAt DESC LIMIT 1', [t.id]);
        if (lastMsg) t.lastMessage = lastMsg.body;
      }
    }

    res.json({ success: true, data: rows });
  } catch (err) { res.status(500).json({ success: false, message: err.message }); }
});

router.get('/:collection/:id', authenticateToken, async (req, res) => {
  const table = getTable(req.params.collection);
  if (!table) return res.status(400).json({ success: false, message: 'Invalid collection' });
  try {
    const row = await db.get(`SELECT * FROM ${table} WHERE id = ?`, [req.params.id]);
    if (!row) return res.status(404).json({ success: false, message: 'Not found' });
    res.json({ success: true, data: row });
  } catch (err) { res.status(500).json({ success: false, message: err.message }); }
});

router.post('/:collection', authenticateToken, async (req, res) => {
  const table = getTable(req.params.collection);
  if (!table) return res.status(400).json({ success: false, message: 'Invalid collection' });
  try {
    const data = { ...req.body };
    if (!data.id) data.id = crypto.randomUUID();
    // Serialize objects
    for (const [k, v] of Object.entries(data)) {
      if (typeof v === 'object' && v !== null) data[k] = JSON.stringify(v);
    }
    const keys = Object.keys(data);
    const values = Object.values(data);
    await db.run(`INSERT INTO ${table} (${keys.join(',')}) VALUES (${keys.map(()=>'?').join(',')})`, values);
    
    // Auto update thread's lastMessageAt when a message is posted
    if (table === 'chat_messages' && data.threadId) {
       const now = new Date().toISOString().replace('T', ' ').slice(0, 19);
       await db.run('UPDATE chat_threads SET lastMessageAt = ?, updatedAt = ? WHERE id = ?', [now, now, data.threadId]);
    }

    res.json({ success: true, data: { id: data.id } });
  } catch (err) { res.status(500).json({ success: false, message: err.message }); }
});

router.put('/:collection/:id', authenticateToken, async (req, res) => {
  const table = getTable(req.params.collection);
  if (!table) return res.status(400).json({ success: false, message: 'Invalid collection' });
  try {
    const data = { ...req.body };
    delete data.id;
    for (const [k, v] of Object.entries(data)) {
      if (typeof v === 'object' && v !== null) data[k] = JSON.stringify(v);
    }
    const keys = Object.keys(data);
    const values = Object.values(data);
    values.push(req.params.id);
    await db.run(`UPDATE ${table} SET ${keys.map(k=>k+'=?').join(',')} WHERE id=?`, values);
    res.json({ success: true });
  } catch (err) { res.status(500).json({ success: false, message: err.message }); }
});

router.delete('/:collection/:id', authenticateToken, async (req, res) => {
  const table = getTable(req.params.collection);
  if (!table) return res.status(400).json({ success: false, message: 'Invalid collection' });
  try {
    await db.run(`DELETE FROM ${table} WHERE id = ?`, [req.params.id]);
    res.json({ success: true });
  } catch (err) { res.status(500).json({ success: false, message: err.message }); }
});

module.exports = router;
