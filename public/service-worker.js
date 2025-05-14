// Cache Names
const CACHE_NAME = 'wizi-learn-cache-v1';
const DYNAMIC_CACHE = 'wizi-learn-dynamic-cache-v1';

// Assets to cache on install
const ASSETS_TO_CACHE = [
  '/',
  '/index.html',
  '/manifest.json',
  '/favicon.ico',
  '/lovable-uploads/e4aa6740-d9f0-40d2-a150-efc75ae46692.png'
];

// Installation event - cache core assets
self.addEventListener('install', (event) => {
  console.log('[Service Worker] Installation');
  
  // Skip waiting to activate the service worker immediately
  self.skipWaiting();
  
  // Cache core assets
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      console.log('[Service Worker] Caching core assets');
      return cache.addAll(ASSETS_TO_CACHE);
    })
  );
});

// Activation event - clean up old caches
self.addEventListener('activate', (event) => {
  console.log('[Service Worker] Activation');
  
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames
          .filter((name) => name !== CACHE_NAME && name !== DYNAMIC_CACHE)
          .map((name) => {
            console.log('[Service Worker] Removing old cache', name);
            return caches.delete(name);
          })
      );
    })
  );
  
  // Ensures the service worker takes control of the page immediately
  return self.clients.claim();
});

// Fetch event - serve from cache first, then network
self.addEventListener('fetch', (event) => {
  // Skip for non-GET requests or browser extensions
  if (event.request.method !== 'GET' || 
      !event.request.url.startsWith('http')) {
    return;
  }
  
  // Skip for API requests
  if (event.request.url.includes('/api/')) {
    return fetch(event.request);
  }
  
  // Cache-first strategy
  event.respondWith(
    caches.match(event.request)
      .then((cachedResponse) => {
        if (cachedResponse) {
          return cachedResponse;
        }
        
        // Not in cache, get from network
        return fetch(event.request)
          .then((networkResponse) => {
            // Cache the response for future
            if (networkResponse && networkResponse.status === 200) {
              const clonedResponse = networkResponse.clone();
              caches.open(DYNAMIC_CACHE).then((cache) => {
                cache.put(event.request, clonedResponse);
              });
            }
            return networkResponse;
          })
          .catch(() => {
            // If both cache and network fail, show a fallback for HTML pages
            if (event.request.headers.get('accept').includes('text/html')) {
              return caches.match('/');
            }
            // For other resources, fail gracefully
            return new Response('Ressource indisponible hors connexion', {
              status: 503,
              headers: { 'Content-Type': 'text/plain' }
            });
          });
      })
  );
});

// Push notifications handler (keeping existing code)
self.addEventListener('push', (event) => {
  console.log('[Service Worker] Notification push reçue', event);

  let notificationData = {};
  if (event.data) {
    try {
      notificationData = event.data.json();
    } catch (e) {
      console.error('Erreur lors du parsing des données de notification', e);
    }
  }

  const title = notificationData.title || 'Notification';
  const options = {
    body: notificationData.body || 'Vous avez reçu une notification.',
    icon: notificationData.icon || '/icons/notification.png',
    badge: notificationData.badge || '/icons/badge.png',
    data: notificationData.data || {}
  };

  event.waitUntil(
    self.registration.showNotification(title, options)
  );
});

// Notification click handler (keeping existing code)
self.addEventListener('notificationclick', (event) => {
  console.log('[Service Worker] Notification cliquée', event);

  event.notification.close();

  // Gérer les actions en fonction du type de notification
  const data = event.notification.data || {};
  let url = '/';

  if (data.type === 'quiz-available' && data.quizId) {
    url = `/quiz/${data.quizId}`;
  } else if (data.type === 'formation-update' && data.formationId) {
    url = `/formation/${data.formationId}`;
  } else if (data.type === 'quiz-completed') {
    url = '/quiz';
  } else if (data.type === 'reward-earned') {
    url = '/profile';
  }

  event.waitUntil(
    clients.openWindow(url)
  );
});
