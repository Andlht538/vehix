const CACHE_NAME = 'auto-manager-v1.2.0';
const STATIC_CACHE_NAME = 'auto-manager-static-v1.2.0';
const DYNAMIC_CACHE_NAME = 'auto-manager-dynamic-v1.2.0';

// Ressources à mettre en cache immédiatement
const STATIC_ASSETS = [
  '/',
  '/dashboard',
  '/vehicles',
  '/css/app.css',
  '/js/app.js',
  '/manifest.json',
  '/icons/icon-192x192.png',
  '/icons/icon-512x512.png',
  // Ajouter les fonts et autres assets statiques
  'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap'
];

// Ressources dynamiques importantes
const IMPORTANT_ROUTES = [
  '/dashboard',
  '/vehicles',
  '/insurances',
  '/repairs',
  '/maintenances',
  '/fuelings',
  '/trips'
];

// Installation du Service Worker
self.addEventListener('install', (event) => {
  console.log('Service Worker: Installing...');
  
  event.waitUntil(
    Promise.all([
      // Cache statique
      caches.open(STATIC_CACHE_NAME).then((cache) => {
        console.log('Service Worker: Caching static assets');
        return cache.addAll(STATIC_ASSETS);
      }),
      // Forcer l'activation immédiate
      self.skipWaiting()
    ])
  );
});

// Activation du Service Worker
self.addEventListener('activate', (event) => {
  console.log('Service Worker: Activating...');

  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cacheName) => {
          if (
            cacheName !== STATIC_CACHE_NAME &&
            cacheName !== DYNAMIC_CACHE_NAME
          ) {
            console.log('Service Worker: Deleting old cache', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );

  // Forcer le Service Worker à prendre le contrôle immédiatement
  return self.clients.claim();
});

// Gestion des requêtes
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);

  // Ne pas gérer les requêtes vers d'autres domaines
  if (url.origin !== location.origin) {
    event.respondWith(fetch(request));
    return;
  }

  // Gérer les routes importantes avec stratégie cache-first
  if (IMPORTANT_ROUTES.some(route => url.pathname.startsWith(route))) {
    event.respondWith(
      caches.match(request).then((response) => {
        return response || fetch(request).then((fetchResponse) => {
          // Mettre à jour le cache dynamique
          if (fetchResponse && fetchResponse.status === 200) {
            const responseClone = fetchResponse.clone();
            caches.open(DYNAMIC_CACHE_NAME).then((cache) => {
              cache.put(request, responseClone);
            });
          }
          return fetchResponse;
        });
      })
    );
  } else {
    // Pour les autres requêtes, utiliser network-first
    event.respondWith(
      fetch(request).then((fetchResponse) => {
        // Si la réponse est bonne, la mettre en cache
        if (fetchResponse && fetchResponse.status === 200) {
          const responseClone = fetchResponse.clone();
          caches.open(DYNAMIC_CACHE_NAME).then((cache) => {
            cache.put(request, responseClone);
          });
        }
        return fetchResponse;
      }).catch(() => {
        // Si la requête échoue, essayer le cache
        return caches.match(request).then((response) => {
          if (response) {
            return response;
          }
          // Retourner une page de fallback pour les routes importantes
          if (IMPORTANT_ROUTES.some(route => url.pathname.startsWith(route))) {
            return caches.match('/');
          }
          return response;
        });
      })
    );
  }
});

// Gestion des messages
self.addEventListener('message', (event) => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
});