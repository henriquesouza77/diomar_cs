/**
 * motion.js — Camada de movimento do site
 * 1. Mapa único de seletores -> animação (nada de atributos espalhados pelo HTML)
 * 2. Barra de progresso de scroll
 * 3. Botões magnéticos nos CTAs principais
 * Roda ANTES de animations.js, que lê os atributos data-reveal / data-stagger.
 */
'use strict';

(() => {
  // [seletor, variante de data-reveal, delay opcional]
  const REVEAL = [
    ['.section-header',            'up'],
    ['.featured__header',          'up'],
    ['.about-section__media',      'left'],
    ['.about-section__content',    'right'],
    ['.quote-section__inner',      'fade'],
    ['.cta-section__inner',        'scale'],
    ['.contact-hero__inner',       'up'],
    ['.contact-form',              'up', 2],
    ['.properties-toolbar',        'fade'],
    ['.filters',                   'left'],
    ['.property-header',           'up'],
    ['.property-gallery',          'scale', 1],
    ['.property-sidebar',          'right', 2],
    ['.property-section',          'up'],
  ];

  // Containers cujos filhos entram em cascata
  const STAGGER = [
    '.contact-info',
    '.footer__columns',
    '.numbers-section__grid',
    '.categories__grid',
    '.about-section__list',
  ];

  // CTAs com efeito magnético (desktop)
  const MAGNETIC = ['.hero__actions .btn', '.cta-section__actions .btn'];

  function mark(selector, fn) {
    document.querySelectorAll(selector).forEach(el => {
      if (el.hasAttribute('data-reveal') || el.hasAttribute('data-stagger')) return;
      if (el.closest('[data-reveal], [data-stagger]')) return; // evita animar pai e filho juntos
      fn(el);
    });
  }

  function init() {
    REVEAL.forEach(([selector, variant, delay]) => mark(selector, el => {
      el.setAttribute('data-reveal', variant);
      if (delay) el.setAttribute('data-delay', String(delay));
    }));

    STAGGER.forEach(selector => mark(selector, el => el.setAttribute('data-stagger', '')));

    MAGNETIC.forEach(selector =>
      document.querySelectorAll(selector).forEach(el => el.classList.add('btn-magnetic'))
    );

    initScrollProgress();
  }

  function initScrollProgress() {
    const bar = document.createElement('div');
    bar.className = 'scroll-progress';
    bar.setAttribute('aria-hidden', 'true');
    document.body.appendChild(bar);

    let ticking = false;
    const update = () => {
      const max = document.documentElement.scrollHeight - window.innerHeight;
      bar.style.transform = `scaleX(${max > 0 ? Math.min(window.scrollY / max, 1) : 0})`;
      ticking = false;
    };

    window.addEventListener('scroll', () => {
      if (!ticking) { requestAnimationFrame(update); ticking = true; }
    }, { passive: true });
    window.addEventListener('resize', update, { passive: true });
    update();
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})();
