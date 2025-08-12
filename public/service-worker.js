
// Service Worker pour gérer les notifications push
self.addEventListener('install', (event) => {
  console.log('[Service Worker] Installation');
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  console.log('[Service Worker] Activation');
  return self.clients.claim();
});

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
