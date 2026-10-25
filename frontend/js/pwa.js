/**
 * pwa.js — Registra Service Worker + gerencia install prompt
 */
'use strict';

(() => {
  // ============================================================
  // 1. REGISTRA SERVICE WORKER
  // ============================================================
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
      navigator.serviceWorker.register(`${window.__BASE_URL__ || ''}/frontend/sw.js`, {
        scope: `${window.__BASE_URL__ || ''}/`,
      })
        .then((reg) => {
          console.log('[PWA] Service Worker registrado:', reg.scope);

          // Verifica atualizações a cada 1 hora
          setInterval(() => reg.update(), 60 * 60 * 1000);
        })
        .catch((err) => {
          console.warn('[PWA] Falha ao registrar SW:', err);
        });
    });
  }

  // ============================================================
  // 2. INSTALL PROMPT — Android/Desktop
  // ============================================================
  let deferredPrompt = null;
  const INSTALL_DISMISSED_KEY = 'pwa_install_dismissed_at';
  const DISMISS_COOLDOWN_DAYS = 7;

  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;

    // Verifica se o usuário já dispensou recentemente
    const dismissedAt = localStorage.getItem(INSTALL_DISMISSED_KEY);
    if (dismissedAt) {
      const daysSince = (Date.now() - parseInt(dismissedAt, 10)) / 86400000;
      if (daysSince < DISMISS_COOLDOWN_DAYS) return;
    }

    showInstallBanner();
  });

  function showInstallBanner() {
    const banner = document.getElementById('pwaInstallBanner');
    if (banner) {
      setTimeout(() => banner.classList.add('is-visible'), 2000);
    }
  }

  document.getElementById('pwaInstallBtn')?.addEventListener('click', async () => {
    if (!deferredPrompt) return;

    deferredPrompt.prompt();
    const { outcome } = await deferredPrompt.userChoice;

    console.log('[PWA] Resultado da instalação:', outcome);
    deferredPrompt = null;

    hideInstallBanner();
  });

  document.getElementById('pwaInstallClose')?.addEventListener('click', () => {
    localStorage.setItem(INSTALL_DISMISSED_KEY, String(Date.now()));
    hideInstallBanner();
  });

  function hideInstallBanner() {
    const banner = document.getElementById('pwaInstallBanner');
    if (banner) banner.classList.remove('is-visible');
  }

  // ============================================================
  // 3. IOS — não tem beforeinstallprompt, mostra banner manual
  // ============================================================
  const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
  const isStandalone = window.matchMedia('(display-mode: standalone)').matches
    || navigator.standalone === true;

  if (isIOS && !isStandalone) {
    const dismissedAt = localStorage.getItem(INSTALL_DISMISSED_KEY);
    let shouldShow = true;

    if (dismissedAt) {
      const daysSince = (Date.now() - parseInt(dismissedAt, 10)) / 86400000;
      if (daysSince < DISMISS_COOLDOWN_DAYS) shouldShow = false;
    }

    if (shouldShow) {
      const banner = document.getElementById('pwaInstallBanner');
      if (banner) {
        banner.classList.add('is-ios');
        setTimeout(() => banner.classList.add('is-visible'), 2000);
      }
    }
  }

  // ============================================================
  // 4. DETECTA MUDANÇA DE CONEXÃO
  // ============================================================
  window.addEventListener('online', () => {
    document.body.classList.remove('is-offline');
    console.log('[PWA] Voltou online');
  });

  window.addEventListener('offline', () => {
    document.body.classList.add('is-offline');
    console.log('[PWA] Ficou offline');
  });

  if (!navigator.onLine) {
    document.body.classList.add('is-offline');
  }
})();