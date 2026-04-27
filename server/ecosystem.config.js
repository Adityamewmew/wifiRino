const path = require('path');

const ROOT_DIR = path.resolve(__dirname, '..');
const SERVER_FILE = path.join(ROOT_DIR, 'server', 'server.js');
const LOG_DIR = path.join(ROOT_DIR, 'logs');

// Cari service account Firebase secara statis
// File JSON ada di e:\APLIKASI\ (satu level di atas BILLING BARU)
const FIREBASE_SA_PATH = path.resolve(ROOT_DIR, '..', 'billing-5858-firebase-adminsdk-fbsvc-99edf18e16.json');

module.exports = {
    apps: [
        {
            name: 'SS-billing',
            cwd: ROOT_DIR,
            script: SERVER_FILE,
            instances: 'max',           // Gunakan semua CPU core (i5 = 4 core/8 thread)
            exec_mode: 'cluster',       // Mode cluster = load balancing otomatis
            watch: false,               // Matikan watch (pakai polling dari frontend)
            max_memory_restart: '512M', // Auto-restart jika RAM > 512MB
            env: {
                NODE_ENV: 'production',
                PORT: 5858,
                // Hardcode path statis agar Firebase berfungsi meski PM2 distart via service/boot
                FIREBASE_SERVICE_ACCOUNT_PATH: FIREBASE_SA_PATH,
                GOOGLE_APPLICATION_CREDENTIALS: FIREBASE_SA_PATH
            },
            error_file: path.join(LOG_DIR, 'pm2-error.log'),
            out_file: path.join(LOG_DIR, 'pm2-out.log'),
            log_date_format: 'YYYY-MM-DD HH:mm:ss',
            restart_delay: 3000,        // Tunggu 3 detik sebelum restart otomatis
            autorestart: true           // Auto-restart jika crash
        }
    ]
};
