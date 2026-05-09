const express = require('express');
const router = express.Router();
const crypto = require('crypto');
const db = require('../config/database');

const nowSql = () => new Date().toISOString().replace('T', ' ').slice(0, 19);

// GET /api/chat/pelanggan/:idPelanggan - Ambil atau buat thread, dan kembalikan pesannya
router.get('/pelanggan/:idPelanggan', async (req, res) => {
  try {
    const { idPelanggan } = req.params;
    if (!idPelanggan) return res.status(400).json({ success: false, message: 'ID Pelanggan diperlukan' });

    // 1. Cari atau buat thread
    let thread = await db.get('SELECT * FROM chat_threads WHERE idPelanggan = ?', [idPelanggan]);
    if (!thread) {
      const tid = crypto.randomUUID();
      const now = nowSql();
      await db.run(
        `INSERT INTO chat_threads (id, idPelanggan, pelangganDbId, lastMessageAt, createdAt, updatedAt) VALUES (?,?,?,?,?,?)`,
        [tid, idPelanggan, null, now, now, now]
      );
      thread = await db.get('SELECT * FROM chat_threads WHERE id = ?', [tid]);
    }

    // 2. Ambil semua pesan untuk thread ini
    const messages = await db.all('SELECT * FROM chat_messages WHERE threadId = ? ORDER BY createdAt ASC', [thread.id]);

    res.json({ success: true, data: { thread, messages } });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

// POST /api/chat/pelanggan/:idPelanggan - Pelanggan mengirim pesan
router.post('/pelanggan/:idPelanggan', async (req, res) => {
  try {
    const { idPelanggan } = req.params;
    const { body } = req.body;
    if (!idPelanggan || !body) return res.status(400).json({ success: false, message: 'Data tidak lengkap' });

    const thread = await db.get('SELECT * FROM chat_threads WHERE idPelanggan = ?', [idPelanggan]);
    if (!thread) return res.status(404).json({ success: false, message: 'Thread belum ada, panggil GET dulu' });

    const msgId = crypto.randomUUID();
    const now = nowSql();

    // Insert message
    await db.run(
      'INSERT INTO chat_messages (id, threadId, senderType, senderUserId, body, createdAt) VALUES (?, ?, ?, ?, ?, ?)',
      [msgId, thread.id, 'pelanggan', idPelanggan, body, now]
    );

    // Update lastMessageAt in thread
    await db.run('UPDATE chat_threads SET lastMessageAt = ?, updatedAt = ? WHERE id = ?', [now, now, thread.id]);

    res.json({ success: true, message: 'Pesan terkirim' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
});

module.exports = router;
