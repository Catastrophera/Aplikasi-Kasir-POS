const CACHE_NAME = 'cheframa-v2'; // Ubah versi cache agar yang lama terhapus
const urlsToCache = [
  '/',
  '/pos',
  '/manifest.json'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        return cache.addAll(urlsToCache);
      })
  );
  self.skipWaiting();
});

self.addEventListener('activate', event => {
  // Hapus cache versi lama
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  self.clients.claim();
});

self.addEventListener('fetch', event => {
  if (event.request.method !== 'GET') return;

  event.respondWith(
    // Strategi: Network First, Fallback to Cache
    fetch(event.request)
      .then(response => {
        // Simpan versi terbaru ke cache
        if (response.ok) {
          const resClone = response.clone();
          caches.open(CACHE_NAME).then(cache => {
            cache.put(event.request, resClone);
          });
        }
        return response;
      })
      .catch(() => {
        // Jika offline atau gagal, ambil dari cache
        return caches.match(event.request).then(response => {
          if (response) return response;
          // Fallback navigasi
          if (event.request.mode === 'navigate') {
            return caches.match('/pos');
          }
        });
      })
  );
});
