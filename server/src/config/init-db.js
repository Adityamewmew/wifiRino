/**
 * MySQL Schema Initialization
 * Migrated from SQLite (server/database.js) → MySQL
 * Logika dan struktur tabel TIDAK diubah, hanya syntax yang disesuaikan.
 */
const db = require('./database');
const bcrypt = require('bcryptjs');
const crypto = require('crypto');

async function initializeDatabase() {
  console.log('🔧 Initializing MySQL database schema...');

  // Users Table
  await db.run(`CREATE TABLE IF NOT EXISTS users (
    id VARCHAR(36) PRIMARY KEY,
    nama VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    noWA VARCHAR(50),
    role VARCHAR(50),
    gaji DOUBLE DEFAULT 0,
    aktif TINYINT DEFAULT 1,
    areas TEXT,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4`);

  // Customers Table
  await db.run(`CREATE TABLE IF NOT EXISTS pelanggan (
    id VARCHAR(36) PRIMARY KEY,
    idPelanggan VARCHAR(100) UNIQUE,
    nama VARCHAR(255),
    noWA VARCHAR(50),
    area VARCHAR(255),
    paket VARCHAR(255),
    hargaPaket DOUBLE DEFAULT 0,
    tglTagih INT DEFAULT 10,
    alamat TEXT,
    status VARCHAR(50) DEFAULT 'aktif',
    idPPOE VARCHAR(100),
    biayaTambahan1 TEXT,
    biayaTambahan2 TEXT,
    diskon TEXT,
    totalFinal DOUBLE DEFAULT 0,
    saldoDeposit DOUBLE DEFAULT 0,
    bulanMulai INT,
    tahunMulai INT,
    tanggalMulaiStr VARCHAR(50),
    mulaiTagihan VARCHAR(50),
    email VARCHAR(255),
    noKtp VARCHAR(50),
    foto1 TEXT,
    latitude DOUBLE,
    longitude DOUBLE,
    keterangan TEXT,
    lamaBerlanggananTeks VARCHAR(255),
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4`);

  // Monthly Bills (Tagihan Bulanan) Table
  await db.run(`CREATE TABLE IF NOT EXISTS tagihan_bulanan (
    id VARCHAR(36) PRIMARY KEY,
    idPelanggan VARCHAR(100),
    namaPelanggan VARCHAR(255),
    area VARCHAR(255),
    paket VARCHAR(255),
    noWA VARCHAR(50),
    bulan INT,
    tahun INT,
    totalTagihan DOUBLE DEFAULT 0,
    status VARCHAR(50) DEFAULT 'belum',
    tglJatuhTempo DATETIME,
    tglIsolir DATETIME,
    diskonSnapshot TEXT,
    biayaSnapshot TEXT,
    tglBayar DATETIME,
    metodeBayar VARCHAR(100),
    dibayar_ke VARCHAR(255),
    dimukaBatchId VARCHAR(100),
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tagihan_pelanggan (idPelanggan),
    INDEX idx_tagihan_bulan_tahun (bulan, tahun),
    INDEX idx_tagihan_status (status)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4`);

  // Area Table
  await db.run(`CREATE TABLE IF NOT EXISTS areas (
    id VARCHAR(36) PRIMARY KEY,
    nama VARCHAR(255) UNIQUE,
    keterangan TEXT,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4`);

  // Packages Table
  await db.run(`CREATE TABLE IF NOT EXISTS paket (
    id VARCHAR(36) PRIMARY KEY,
    nama VARCHAR(255) UNIQUE,
    harga DOUBLE DEFAULT 0,
    deskripsi TEXT,
    aktif TINYINT DEFAULT 1,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4`);

  // Bookkeeping (Pembukuan) Table
  await db.run(`CREATE TABLE IF NOT EXISTS pembukuan (
    id VARCHAR(36) PRIMARY KEY,
    tanggal DATETIME,
    jenis VARCHAR(50),
    kategori VARCHAR(100),
    nominal DOUBLE DEFAULT 0,
    keterangan TEXT,
    idReferensi VARCHAR(100),
    createdBy VARCHAR(255),
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_pembukuan_jenis (jenis),
    INDEX idx_pembukuan_kategori (kategori),
    INDEX idx_pembukuan_tanggal (tanggal)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4`);

  // Audit Logs Table
  await db.run(`CREATE TABLE IF NOT EXISTS audit_logs (
    id VARCHAR(36) PRIMARY KEY,
    tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
    userEmail VARCHAR(255),
    userRole VARCHAR(50),
    aksi VARCHAR(100),
    entitas VARCHAR(100),
    idData VARCHAR(100),
    keterangan TEXT,
    INDEX idx_audit_tanggal (tanggal)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4`);

  // App Settings Table
  await db.run(`CREATE TABLE IF NOT EXISTS pengaturan (
    kunci VARCHAR(100) PRIMARY KEY,
    nilai TEXT
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4`);

  // Customer Announcement Broadcasts
  await db.run(`CREATE TABLE IF NOT EXISTS pengumuman (
    id VARCHAR(36) PRIMARY KEY,
    targetType VARCHAR(50) DEFAULT 'global',
    targetAreaId VARCHAR(36),
    targetAreaName VARCHAR(255),
    targetPelangganIds TEXT,
    pesan TEXT,
    startAt DATETIME,
    endAt DATETIME,
    aktif TINYINT DEFAULT 1,
    createdBy VARCHAR(255),
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4`);

  // Push Notification Tokens (FCM)
  await db.run(`CREATE TABLE IF NOT EXISTS push_tokens (
    id VARCHAR(36) PRIMARY KEY,
    token VARCHAR(500) UNIQUE,
    targetType VARCHAR(50) DEFAULT 'global',
    targetId VARCHAR(100),
    roleKey VARCHAR(50),
    platform VARCHAR(50),
    deviceInfo TEXT,
    isActive TINYINT DEFAULT 1,
    lastSeen DATETIME,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_push_active (isActive),
    INDEX idx_push_target (targetType, targetId)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4`);

  // Push Notification Dispatch Logs
  await db.run(`CREATE TABLE IF NOT EXISTS push_dispatch_logs (
    id VARCHAR(36) PRIMARY KEY,
    eventKey VARCHAR(255) UNIQUE,
    kategori VARCHAR(100),
    targetType VARCHAR(50),
    targetId VARCHAR(100),
    refId VARCHAR(100),
    title VARCHAR(255),
    body TEXT,
    payload TEXT,
    successCount INT DEFAULT 0,
    failedCount INT DEFAULT 0,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4`);

  // Pending delete requests
  await db.run(`CREATE TABLE IF NOT EXISTS pending_delete_requests (
    id VARCHAR(36) PRIMARY KEY,
    requestType VARCHAR(50),
    targetId VARCHAR(100),
    targetLabel VARCHAR(255),
    reason TEXT,
    status VARCHAR(50) DEFAULT 'pending',
    requestedByUid VARCHAR(36),
    requestedByEmail VARCHAR(255),
    requestedByRole VARCHAR(50),
    approvedByUid VARCHAR(36),
    approvedByEmail VARCHAR(255),
    approvedAt DATETIME,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4`);

  // Technician Tasks Table
  await db.run(`CREATE TABLE IF NOT EXISTS tugas_teknisi (
    id VARCHAR(36) PRIMARY KEY,
    judul VARCHAR(255),
    deskripsi TEXT,
    jenisTask VARCHAR(100),
    prioritas VARCHAR(50) DEFAULT 'normal',
    status VARCHAR(50) DEFAULT 'pending',
    assignTo VARCHAR(36),
    assignToNama VARCHAR(255),
    isBroadcast TINYINT DEFAULT 0,
    claimedBy VARCHAR(36),
    claimedByNama VARCHAR(255),
    claimedAt DATETIME,
    idPelanggan VARCHAR(100),
    namaPelanggan VARCHAR(255),
    alamat TEXT,
    noWA VARCHAR(50),
    tglDibuat DATETIME,
    tglDeadline DATETIME,
    tglSelesai DATETIME,
    catatanTeknisi TEXT,
    createdBy VARCHAR(255),
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tugas_status (status),
    INDEX idx_tugas_assign (assignTo)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4`);

  // Chat threads
  await db.run(`CREATE TABLE IF NOT EXISTS chat_threads (
    id VARCHAR(36) PRIMARY KEY,
    idPelanggan VARCHAR(100) NOT NULL UNIQUE,
    pelangganDbId VARCHAR(36),
    assignedUserId VARCHAR(36),
    assignedAt DATETIME,
    lastMessageAt DATETIME,
    fieldPublicUntil DATETIME,
    fieldPublicCanReply TINYINT DEFAULT 0,
    delegatedToJson TEXT,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4`);

  // Chat messages
  await db.run(`CREATE TABLE IF NOT EXISTS chat_messages (
    id VARCHAR(36) PRIMARY KEY,
    threadId VARCHAR(36) NOT NULL,
    senderType VARCHAR(50) NOT NULL,
    senderUserId VARCHAR(36),
    body TEXT NOT NULL,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_chat_messages_thread (threadId)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4`);

  // Chat staff participants
  await db.run(`CREATE TABLE IF NOT EXISTS chat_staff_participants (
    threadId VARCHAR(36) NOT NULL,
    userId VARCHAR(36) NOT NULL,
    firstReplyAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (threadId, userId)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4`);

  // Mikrotik routers
  await db.run(`CREATE TABLE IF NOT EXISTS mikrotik_routers (
    id VARCHAR(36) PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    host VARCHAR(255),
    apiPort INT DEFAULT 8728,
    apiUser VARCHAR(100),
    apiPassword VARCHAR(255),
    keterangan TEXT,
    rosVersi VARCHAR(50),
    userManager VARCHAR(100),
    hotspotManager VARCHAR(100),
    serviceType VARCHAR(100),
    lastProbeAt DATETIME,
    lastProbeMs INT,
    lastProbeOk TINYINT,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4`);

  // Pelanggan mikrotik mapping
  await db.run(`CREATE TABLE IF NOT EXISTS pelanggan_mikrotik (
    id VARCHAR(36) PRIMARY KEY,
    pelangganDbId VARCHAR(36) NOT NULL UNIQUE,
    routerId VARCHAR(36),
    profile VARCHAR(100),
    ipPool VARCHAR(100),
    isolirAddressList VARCHAR(100) DEFAULT 'isolir-billing',
    simpleQueueName VARCHAR(255),
    catatanTeknis TEXT,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_pelanggan_mikrotik_router (routerId)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4`);

  // Seed default settings
  const [settingsCount] = await db.query('SELECT COUNT(*) AS count FROM pengaturan');
  if (settingsCount[0].count === 0) {
    const defaults = [
      { kunci: 'payment_bca_no', nilai: '8732 1199 00' },
      { kunci: 'payment_bca_nama', nilai: 'PT Sans Speed' },
      { kunci: 'payment_bca_aktif', nilai: '1' },
      { kunci: 'payment_mdr_no', nilai: '1370 0011 2233' },
      { kunci: 'payment_mdr_nama', nilai: 'PT Sans Speed' },
      { kunci: 'payment_mdr_aktif', nilai: '1' },
      { kunci: 'payment_dana_no', nilai: '0812 3456 7890' },
      { kunci: 'payment_dana_nama', nilai: 'Admin Sans Speed' },
      { kunci: 'payment_dana_aktif', nilai: '1' },
      { kunci: 'payment_wa_cs', nilai: '628123456789' },
    ];
    for (const d of defaults) {
      await db.run('INSERT IGNORE INTO pengaturan (kunci, nilai) VALUES (?, ?)', [d.kunci, d.nilai]);
    }
    console.log('✅ Default settings seeded.');
  }

  // Seed default admin
  const [usersCount] = await db.query('SELECT COUNT(*) AS count FROM users');
  if (usersCount[0].count === 0) {
    const hash = await bcrypt.hash('admin123', 10);
    const uuid = crypto.randomUUID();
    await db.run(
      'INSERT INTO users (id, nama, email, password, role) VALUES (?, ?, ?, ?, ?)',
      [uuid, 'Administrator', 'admin@rinonet.local', hash, 'owner']
    );
    console.log('✅ Default Owner created: admin@rinonet.local / admin123');
  }

  console.log('✅ MySQL database schema initialized successfully.');
}

module.exports = { initializeDatabase };
