const mysql = require('mysql2/promise');
require('dotenv').config({ path: require('path').join(__dirname, '../../.env') });

const pool = mysql.createPool({
  host:     process.env.DB_HOST || 'localhost',
  user:     process.env.DB_USER || 'root',
  password: process.env.DB_PASS || '',
  database: process.env.DB_NAME || 'rinonet_billing',
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0,
  charset: 'utf8mb4',
});

// Helper wrappers compatible with old SQLite API shape
const db = {
  pool,

  // Execute query, returns [rows, fields]
  query: async (sql, params = []) => {
    const [rows, fields] = await pool.query(sql, params);
    return [rows, fields];
  },

  // Get single row
  get: async (sql, params = []) => {
    const [rows] = await pool.query(sql, params);
    return rows[0] || null;
  },

  // Get all rows
  all: async (sql, params = []) => {
    const [rows] = await pool.query(sql, params);
    return rows;
  },

  // Run insert/update/delete, returns { insertId, affectedRows, changedRows }
  run: async (sql, params = []) => {
    const [result] = await pool.query(sql, params);
    return {
      id: result.insertId,
      changes: result.affectedRows,
      insertId: result.insertId,
      affectedRows: result.affectedRows,
      changedRows: result.changedRows,
    };
  },
};

module.exports = db;
