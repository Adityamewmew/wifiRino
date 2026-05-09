/**
 * Migrasi sekali: salin nilai lama cnet_* → ss_* (Sans Speed) agar sesi & tema tetap.
 * Muat skrip ini di <head> sebelum skrip lain yang membaca storage.
 * Catatan: nama kunci lama di sini sengaja memakai string terpisah agar tidak tertimpa pengganti massal.
 */
(function () {
    var C = 'cnet_';
    var S = 'ss_';
    var keys = [
        'theme',
        'token',
        'user',
        'api_base',
        'customer',
        'notifikasi_db',
        'public_packages_cache',
        'sidebar_logo_data',
        'mobile_fcm_token',
        'wa_config',
        'printer_config',
        'payment_wa_cs',
        'front_stats',
        'pelanggan_session'
    ];
    function mig(store) {
        try {
            for (var i = 0; i < keys.length; i++) {
                var oldK = C + keys[i];
                var newK = S + keys[i];
                var o = store.getItem(oldK);
                if (o != null && store.getItem(newK) == null) store.setItem(newK, o);
            }
        } catch (e) { /* */ }
    }
    mig(localStorage);
    mig(sessionStorage);
})();

/** Tema gelap/terang: pastikan <html> dan <body> sama (style.css memakai body.light-mode). */
(function applyStoredSsTheme() {
    function sync() {
        try {
            var light = localStorage.getItem('ss_theme') === 'light';
            if (light) {
                document.documentElement.classList.add('light-mode');
                if (document.body) document.body.classList.add('light-mode');
            } else {
                document.documentElement.classList.remove('light-mode');
                if (document.body) document.body.classList.remove('light-mode');
            }
        } catch (e) { /* */ }
    }
    sync();
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', sync);
    }
})();
