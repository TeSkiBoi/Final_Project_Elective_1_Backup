// Service Worker for caching and offline support
const CACHE_NAME = 'marsu-cache-v1';
const URLS_TO_CACHE = [
    '/',
    '/index.html',
    '/css/bootstrap.min.css',
    '/js/bootstrap.bundle.min.js',
    '/assets/img/CICS LOGO-min.png',
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css'
];

// Install event - cache critical resources
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(URLS_TO_CACHE))
    );
});

// Fetch event - serve from cache, fall back to network
self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                if (response) {
                    return response;
                }
                return fetch(event.request)
                    .then(response => {
                        // Cache new resources
                        if (response.ok && event.request.method === 'GET') {
                            const responseToCache = response.clone();
                            caches.open(CACHE_NAME)
                                .then(cache => {
                                    cache.put(event.request, responseToCache);
                                });
                        }
                        return response;
                    });
            })
    );
});