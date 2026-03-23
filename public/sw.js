const CACHE_NAME = 'vis-v1';
const STATIC_ASSETS = [
    '/css/app.css',
    '/js/app.js',
    '/js/inspection-wizard.js',
    '/js/inspection-show.js',
    '/js/templates-edit.js',
    '/js/settings.js',
    '/js/audit-logs.js',
    '/offline',
];

// Install — cache static assets
self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(CACHE_NAME).then(function(cache) {
            return cache.addAll(STATIC_ASSETS);
        })
    );
    self.skipWaiting();
});

// Activate — clean old caches
self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys().then(function(keys) {
            return Promise.all(
                keys.filter(function(key) { return key !== CACHE_NAME; })
                    .map(function(key) { return caches.delete(key); })
            );
        })
    );
    self.clients.claim();
});

// Fetch — network first, fallback to cache
self.addEventListener('fetch', function(event) {
    var request = event.request;

    // Skip non-GET requests
    if (request.method !== 'GET') return;

    // Skip API/auth requests
    if (request.url.includes('/login') || request.url.includes('/logout') || request.url.includes('/api/')) return;

    event.respondWith(
        fetch(request)
            .then(function(response) {
                // Cache successful responses for static assets
                if (response.ok && (request.url.match(/\.(css|js|png|jpg|jpeg|webp|svg|woff2?)$/))) {
                    var clone = response.clone();
                    caches.open(CACHE_NAME).then(function(cache) {
                        cache.put(request, clone);
                    });
                }
                return response;
            })
            .catch(function() {
                // Offline — try cache
                return caches.match(request).then(function(cached) {
                    if (cached) return cached;
                    // Show offline page for navigation requests
                    if (request.mode === 'navigate') {
                        return caches.match('/offline');
                    }
                    return new Response('', { status: 503 });
                });
            })
    );
});