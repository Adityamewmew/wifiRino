self.addEventListener('install', event => {
    // Force the new service worker to activate immediately
    self.skipWaiting();
});

self.addEventListener('activate', event => {
    // Clear all caches on activation
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    return caches.delete(cacheName);
                })
            );
        }).then(() => {
            // Unregister this service worker
            return self.registration.unregister();
        }).then(() => {
            // Force all clients to reload and use the network
            return clients.claim();
        })
    );
});

self.addEventListener('fetch', event => {
    // Always fetch from network, bypass cache completely
    event.respondWith(fetch(event.request));
});
