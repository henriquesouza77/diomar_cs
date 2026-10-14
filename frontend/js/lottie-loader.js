/**
 * lottie-loader.js — Carrega animações Lottie sob demanda
 * Usa CDN do lottie-web apenas quando há elementos <div data-lottie="...">
 */
'use strict';

(() => {
  const placeholders = document.querySelectorAll('[data-lottie]');
  if (!placeholders.length) return;

  // Detecta se o usuário prefere menos movimento
  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (prefersReduced) return;

  // Carrega o script do Lottie via CDN
  const script = document.createElement('script');
  script.src = 'https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js';
  script.async = true;
  script.onload = initLotties;
  document.head.appendChild(script);

  function initLotties() {
    if (typeof lottie === 'undefined') return;

    placeholders.forEach(el => {
      const path = el.dataset.lottie;
      if (!path) return;

      const loop = el.dataset.lottieLoop !== 'false';
      const autoplay = el.dataset.lottieAutoplay !== 'false';

      lottie.loadAnimation({
        container: el,
        renderer: 'svg',
        loop: loop,
        autoplay: autoplay,
        path: `${API?.BASE_URL || ''}/frontend/assets/lotties/${path}.json`,
      });
    });
  }
})();