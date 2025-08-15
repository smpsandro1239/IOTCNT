// Service Worker para IOTCNT PWA
const CACHE_NAME = 'iotcnt-v2.0.0';
const urlsToCache = [
  '/mobile-app.html',
  '/manifest.json',
  '/icons/icon-192x192.png',
  '/api.php',
  '/database-manager.php',
  '/esp32-integration.php'
];

// Instalar Service Worker
self.addEventListener('install', event => {
  console.log('Service Worker: Instalando...');

  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Service Worker: Cache aberto');
        return cache.addAll(urlsToCache);
      })
      .catch(error => {
        console.error('Service Worker: Erro ao cachear:', error);
      })
  );
});

// Activar Service Worker
self.addEventListener('activate', event => {
  console.log('Service Worker: Activando...');

  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            console.log('Service Worker: Removendo cache antigo:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});

// Interceptar pedidos de rede
self.addEventListener('fetch', event => {
  // Estratégia: Network First com fallback para cache
  if (event.request.url.includes('/api') ||
      event.request.url.includes('/database-manager') ||
      event.request.url.includes('/esp32-integration')) {

    // Para APIs: tentar rede primeiro, depois cache
    event.respondWith(
      fetch(event.request)
        .then(response => {
          // Se a resposta for válida, clonar e cachear
          if (response.status === 200) {
            const responseClone = response.clone();
            caches.open(CACHE_NAME)
              .then(cache => {
                cache.put(event.request, responseClone);
              });
          }
          return response;
        })
        .catch(() => {
          // Se a rede falhar, tentar cache
          return caches.match(event.request)
            .then(cachedResponse => {
              if (cachedResponse) {
                return cachedResponse;
              }
              // Retornar resposta offline padrão para APIs
              return new Response(JSON.stringify({
                status: 'offline',
                message: 'Dados não disponíveis offline',
                cached: true,
                timestamp: new Date().toISOString()
              }), {
                headers: { 'Content-Type': 'application/json' }
              });
            });
        })
    );
  } else {
    // Para outros recursos: Cache First
    event.respondWith(
      caches.match(event.request)
        .then(cachedResponse => {
          if (cachedResponse) {
            return cachedResponse;
          }
          return fetch(event.request);
        })
    );
  }
});

// Sincronização em background
self.addEventListener('sync', event => {
  console.log('Service Worker: Sincronização em background');

  if (event.tag === 'background-sync') {
    event.waitUntil(
      syncData()
    );
  }
});

// Notificações push
self.addEventListener('push', event => {
  console.log('Service Worker: Notificação push recebida');

  const options = {
    body: event.data ? event.data.text() : 'Nova notificação IOTCNT',
    icon: '/icons/icon-192x192.png',
    badge: '/icons/icon-72x72.png',
    vibrate: [200, 100, 200],
    data: {
      dateOfArrival: Date.now(),
      primaryKey: 1
    },
    actions: [
      {
        action: 'explore',
        title: 'Ver Detalhes',
        icon: '/icons/icon-96x96.png'
      },
      {
        action: 'close',
        title: 'Fechar',
        icon: '/icons/icon-96x96.png'
      }
    ]
  };

  event.waitUntil(
    self.registration.showNotification('IOTCNT', options)
  );
});

// Clique em notificação
self.addEventListener('notificationclick', event => {
  console.log('Service Worker: Clique em notificação');

  event.notification.close();

  if (event.action === 'explore') {
    event.waitUntil(
      clients.openWindow('/mobile-app.html')
    );
  }
});

// Função para sincronizar dados
async function syncData() {
  try {
    console.log('Service Worker: Sincronizando dados...');

    // Tentar sincronizar dados das válvulas
    const valvesResponse = await fetch('/api.php?action=valves');
    if (valvesResponse.ok) {
      const cache = await caches.open(CACHE_NAME);
      await cache.put('/api.php?action=valves', valvesResponse.clone());
    }

    // Tentar sincronizar dados do ESP32
    const esp32Response = await fetch('/esp32-integration.php?action=status');
    if (esp32Response.ok) {
      const cache = await caches.open(CACHE_NAME);
      await cache.put('/esp32-integration.php?action=status', esp32Response.clone());
    }

    console.log('Service Worker: Sincronização concluída');

  } catch (error) {
    console.error('Service Worker: Erro na sincronização:', error);
  }
}

// Limpeza periódica do cache
self.addEventListener('message', event => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }

  if (event.data && event.data.type === 'CLEAN_CACHE') {
    event.waitUntil(
      caches.keys().then(cacheNames => {
        return Promise.all(
          cacheNames.map(cacheName => {
            if (cacheName.startsWith('iotcnt-') && cacheName !== CACHE_NAME) {
              return caches.delete(cacheName);
            }
          })
        );
      })
    );
  }
});

// Gestão de quota de armazenamento
self.addEventListener('quotaexceeded', event => {
  console.warn('Service Worker: Quota de armazenamento excedida');

  // Limpar caches antigos
  event.waitUntil(
    caches.keys().then(cacheNames => {
      const oldCaches = cacheNames.filter(name =>
        name.startsWith('iotcnt-') && name !== CACHE_NAME
      );

      return Promise.all(
        oldCaches.map(cacheName => caches.delete(cacheName))
      );
    })
  );
});

// Log de instalação
console.log('Service Worker: Carregado para IOTCNT PWA v2.0.0');
