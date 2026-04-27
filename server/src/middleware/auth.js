/**
 * JWT Authentication Middleware
 * Migrated from server.js authenticateToken function — logika tidak berubah.
 */
const jwt = require('jsonwebtoken');
const db = require('../config/database');

const JWT_SECRET = process.env.JWT_SECRET || 'SS-billing-super-secret-key-2026';

// Role constants and helpers (shared across routes)
const ROLE_ALIAS_TO_KEY = {
  superadmin: 'owner', admin: 'admin_keuangan', 'admin keuangan': 'admin_keuangan',
  'admin-keuangan': 'admin_keuangan', adminkeuangan: 'admin_keuangan', owner: 'owner',
  admin_keuangan: 'admin_keuangan', admin_kasir: 'admin_kasir', 'admin kasir': 'admin_kasir',
  'admin-kasir': 'admin_kasir', adminkasir: 'admin_kasir', kasir: 'admin_kasir',
  teknisi: 'teknisi', penagih: 'penagih', tekpen: 'tekpen', teknisipenagih: 'tekpen'
};

const ROLE_KEY_TO_COMPAT = {
  owner: 'superadmin', admin_keuangan: 'admin', admin_kasir: 'admin',
  teknisi: 'teknisi', penagih: 'penagih', tekpen: 'tekpen'
};

const ROLE_PERMISSIONS = {
  owner: ['access_admin_app', 'view_finance_totals', 'view_finance_reports', 'collect_customer_payment', 'view_customer_bill_amount', 'manage_settings', 'manage_settings_wa', 'manage_users', 'manage_backup_audit', 'manage_tasks'],
  admin_keuangan: ['access_admin_app', 'view_finance_totals', 'view_finance_reports', 'collect_customer_payment', 'view_customer_bill_amount', 'manage_settings', 'manage_settings_wa', 'manage_users', 'manage_backup_audit', 'manage_tasks'],
  admin_kasir: ['access_admin_app', 'collect_customer_payment', 'view_customer_bill_amount', 'manage_tasks', 'manage_settings_wa'],
};

const TEKNISI_ROLES = ['teknisi', 'penagih', 'tekpen', 'teknisipenagih'];
const COLLECTOR_ROLES = ['penagih', 'tekpen', 'teknisipenagih'];
const OFFICE_STAFF_ROLE_KEYS = new Set(['owner', 'admin_keuangan', 'admin_kasir']);
const FIELD_STAFF_ROLE_KEYS = new Set(['teknisi', 'penagih', 'tekpen']);

const normText = (v) => String(v || '').trim().toLowerCase();
const resolveRoleKey = (role) => ROLE_ALIAS_TO_KEY[normText(role)] || normText(role);
const roleToCompat = (role) => ROLE_KEY_TO_COMPAT[resolveRoleKey(role)] || resolveRoleKey(role);
const getPermissionsByRole = (role) => ROLE_PERMISSIONS[resolveRoleKey(role)] || [];
const resolveUserRoleKey = (userLike = {}) => resolveRoleKey(userLike.roleKey || userLike.role);

const hasPermission = (userLike = {}, permission) => {
  const explicit = Array.isArray(userLike.permissions) ? userLike.permissions : null;
  const source = explicit && explicit.length ? explicit : getPermissionsByRole(resolveUserRoleKey(userLike));
  return source.includes(permission);
};

const isAdminKeuanganOrOwner = (userLike = {}) => {
  const roleKey = resolveUserRoleKey(userLike);
  if (userLike && Object.prototype.hasOwnProperty.call(userLike, 'roleKey')) {
    return ['owner', 'admin_keuangan'].includes(roleKey);
  }
  const roleRaw = normText(userLike.role);
  return ['owner', 'admin_keuangan'].includes(roleKey) ||
    ['superadmin', 'admin', 'owner', 'admin_keuangan', 'admin keuangan', 'admin-keuangan', 'adminkeuangan'].includes(roleRaw);
};

const canViewFinanceTotals = (u = {}) => hasPermission(u, 'view_finance_totals') || isAdminKeuanganOrOwner(u);
const canManageSettings = (u = {}) => hasPermission(u, 'manage_settings') || hasPermission(u, 'manage_settings_wa') || isAdminKeuanganOrOwner(u);
const canViewIntegrationSecrets = (u = {}) => hasPermission(u, 'manage_settings') || isAdminKeuanganOrOwner(u);
const isAdminRole = (role) => hasPermission({ role }, 'access_admin_app');
const canCollectPayment = (u = {}) => hasPermission(u, 'collect_customer_payment') || isAdminKeuanganOrOwner(u);
const isOwnerUser = (u = {}) => resolveUserRoleKey(u) === 'owner' || ['owner', 'superadmin'].includes(normText(u.role));
const isTeknisiRole = (role) => TEKNISI_ROLES.includes(normText(role));
const isOfficeStaffUser = (u) => OFFICE_STAFF_ROLE_KEYS.has(resolveUserRoleKey(u));
const isFieldStaffUser = (u) => FIELD_STAFF_ROLE_KEYS.has(resolveUserRoleKey(u));

function buildAuthUserProfile(userRow, resolvedAreas = []) {
  const roleKey = resolveRoleKey(userRow?.role);
  return {
    id: userRow.id, uid: userRow.id, email: userRow.email, nama: userRow.nama,
    role: roleToCompat(roleKey), roleKey, permissions: getPermissionsByRole(roleKey),
    aktif: userRow.aktif, areas: resolvedAreas,
  };
}

// Main middleware
function authenticateToken(req, res, next) {
  const authHeader = req.headers['authorization'];
  const token = authHeader && authHeader.split(' ')[1];
  if (!token) return res.sendStatus(401);

  jwt.verify(token, JWT_SECRET, async (err, user) => {
    if (err) return res.sendStatus(403);
    try {
      const liveUser = await db.get('SELECT id, email, role, aktif FROM users WHERE id = ?', [user.uid]);
      if (!liveUser || Number(liveUser.aktif) === 0) return res.sendStatus(401);
      req.user = {
        ...user,
        uid: user.uid || liveUser.id,
        email: user.email || liveUser.email,
        roleKey: user.roleKey || resolveRoleKey(liveUser.role),
        role: user.role || roleToCompat(liveUser.role),
      };
      next();
    } catch {
      return res.sendStatus(500);
    }
  });
}

async function tryLoadRequestUser(req) {
  const authHeader = req.headers['authorization'];
  const token = authHeader && authHeader.split(' ')[1];
  if (!token) return null;
  try {
    const user = jwt.verify(token, JWT_SECRET);
    const liveUser = await db.get('SELECT id, email, role, aktif FROM users WHERE id = ?', [user.uid]);
    if (!liveUser || Number(liveUser.aktif) === 0) return null;
    return {
      ...user, uid: user.uid || liveUser.id, email: user.email || liveUser.email,
      roleKey: user.roleKey || resolveRoleKey(liveUser.role),
      role: user.role || roleToCompat(liveUser.role),
    };
  } catch { return null; }
}

function tryDecodeAuthUser(req) {
  try {
    const authHeader = req.headers['authorization'];
    const token = authHeader && authHeader.split(' ')[1];
    if (!token) return null;
    return jwt.verify(token, JWT_SECRET);
  } catch { return null; }
}

function ensureAdmin(req, res) {
  if (!req.user) return res.status(401).json({ error: 'Unauthorized' });
  if (!isAdminRole(req.user.role)) return res.status(403).json({ error: 'Akses ditolak. Hanya admin.' });
  return null;
}

module.exports = {
  JWT_SECRET, authenticateToken, tryLoadRequestUser, tryDecodeAuthUser, ensureAdmin,
  buildAuthUserProfile,
  // Role helpers
  resolveRoleKey, roleToCompat, getPermissionsByRole, resolveUserRoleKey,
  hasPermission, isAdminKeuanganOrOwner, canViewFinanceTotals, canManageSettings,
  canViewIntegrationSecrets, isAdminRole, canCollectPayment, isOwnerUser,
  isTeknisiRole, isOfficeStaffUser, isFieldStaffUser, normText,
  TEKNISI_ROLES, COLLECTOR_ROLES, OFFICE_STAFF_ROLE_KEYS, FIELD_STAFF_ROLE_KEYS,
};
