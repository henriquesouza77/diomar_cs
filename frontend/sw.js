/**
 * Service Worker — DIOMAR_CS
 * Estratégias:
 * - HTML/PHP: network-first com fallback para cache
 * - API: sempre network (nunca cacheia dados sensíveis)
 * - CSS/JS/Imagens/Fontes: cache-first
 * - Offline: mostra página /offline.html
 */
'use strict';

const CACHE_VERSION = 'diomar-v1';
const CACHE_STATIC  = `${CACHE_VERSION}-static`;
const CACHE_PAGES   = `${CACHE_VERSION}-pages`;
const CACHE_IMAGES  = `${CACHE_VERSION}-images`;

const PRECACHE_URLS = [
  '/diomar-cs/',
  '/diomar-cs/frontend/pages/index.php',
  '/diomar-cs/frontend/pages/imoveis.php',
  '/diomar-cs/frontend/pages/offline.php',
  '/diomar-cs/frontend/images/placeholder.svg',
  '/diomar-cs/frontend/images/favicon.svg',
  '/diomar-cs/frontend/manifest.json',
];

// ============================================================
// INSTALL — pré-cacheia o essencial
// ============================================================
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_STATIC)
      .then(cache => cache.addAll(PRECACHE_URLS).catch(() => null))
      .then(() => self.skipWaiting())
  );
});

// ============================================================
// ACTIVATE — limpa caches antigos
// ============================================================
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys()
      .then(keys => Promise.all(
        keys
          .filter(key => !key.startsWith(CACHE_VERSION))
          .map(key => caches.delete(key))
      ))
      .then(() => self.clients.claim())
  );
});

// ============================================================
// FETCH — roteia requisições
// ============================================================
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = new URL(request.url);

  // Ignora métodos que não são GET
  if (request.method !== 'GET') return;

  // Ignora requisições externas (analytics, fontes externas)
  if (url.origin !== location.origin) return;

  // Ignora admin (dados sensíveis)
  if (url.pathname.includes('/admin/')) {
    return event.respondWith(fetch(request));
  }

  // Estratégia para API — sempre network
  if (url.pathname.includes('/backend/api.php')) {
    return event.respondWith(
      fetch(request).catch(() => new Response(
        JSON.stringify({ success: false, error: 'Você está offline.' }),
        { headers: { 'Content-Type': 'application/json' }, status: 503 }
      ))
    );
  }

  // Estratégia para imagens — cache-first
  if (/\.(png|jpg|jpeg|webp|svg|gif|avif|ico)$/i.test(url.pathname)) {
    return event.respondWith(cacheFirst(request, CACHE_IMAGES));
  }

  // Estratégia para CSS/JS — cache-first
  if (/\.(css|js|woff|woff2|ttf|eot)$/i.test(url.pathname)) {
    return event.respondWith(cacheFirst(request, CACHE_STATIC));
  }

  // Estratégia para páginas — network-first
  if (url.pathname.endsWith('.php') || url.pathname.endsWith('/')) {
    return event.respondWith(networkFirst(request, CACHE_PAGES));
  }

  // Padrão — cache-first
  return event.respondWith(cacheFirst(request, CACHE_STATIC));
});

// ============================================================
// ESTRATÉGIAS
// ============================================================
async function cacheFirst(request, cacheName) {
  const cache = await caches.open(cacheName);
  const cached = await cache.match(request);
  if (cached) return cached;

  try {
    const response = await fetch(request);
    if (response.ok) cache.put(request, response.clone());
    return response;
  } catch (err) {
    return new Response('', { status: 503 });
  }
}

async function networkFirst(request, cacheName) {
  const cache = await caches.open(cacheName);

  try {
    const response = await fetch(request);
    if (response.ok) cache.put(request, response.clone());
    return response;
  } catch (err) {
    const cached = await cache.match(request);
    if (cached) return cached;

    // Fallback para página offline
    const offline = await caches.match('/diomar-cs/frontend/pages/offline.php');
    return offline || new Response('Offline', { status: 503 });
  }
}

// ============================================================
// MENSAGENS (para atualizar o SW)
// ============================================================
self.addEventListener('message', (event) => {
  if (event.data?.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
});