/**
 * dock-nav.js — Transição fluida do menu (dock desktop + barra inferior mobile)
 * - Um indicador único desliza até o item clicado (desktop)
 * - O item clicado já vira "ativo" (rótulo expande) antes da página trocar
 * - A navegação espera a animação terminar (~380ms)
 * A troca de página em si fica por conta de @view-transition (21-dock-nav.css).
 */
'use strict';

(() => {
  const NAV_DELAY = 380;
  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  const menu = document.querySelector('#navbar .navbar__menu');
  const dockLinks = menu ? [...menu.querySelectorAll('.navbar__link')] : [];
  const bottomLinks = [...document.querySelectorAll('.bottom-nav__item')];

  // ------------------------------------------------------------------
  // Indicador deslizante (desktop)
  // ------------------------------------------------------------------
  let indicator = null;
  let cur = { x: 0, w: 0 };
  let rafId = null;
  let settleUntil = 0;

  function target() {
    const active = menu.querySelector('.navbar__link.is-active');
    if (!active) return null;
    const m = menu.getBoundingClientRect();
    const r = active.getBoundingClientRect();
    return { x: r.left - m.left - menu.clientLeft, w: r.width };
  }

  function paint() {
    indicator.style.width = `${cur.w}px`;
    indicator.style.transform = `translateX(${cur.x}px)`;
  }

  function step() {
    const t = target();
    if (!t) { indicator.style.opacity = '0'; rafId = null; return; }
    indicator.style.opacity = '1';

    // Segue o alvo com suavização; o alvo também se mexe enquanto o rótulo expande
    cur.x += (t.x - cur.x) * 0.22;
    cur.w += (t.w - cur.w) * 0.22;
    paint();

    const settled = Math.abs(t.x - cur.x) < 0.3 && Math.abs(t.w - cur.w) < 0.3;
    if (settled && performance.now() > settleUntil) { cur = { ...t }; paint(); rafId = null; return; }
    rafId = requestAnimationFrame(step);
  }

  function follow(ms = 900) {
    settleUntil = performance.now() + ms;
    if (!rafId) rafId = requestAnimationFrame(step);
  }

  function snap() {
    const t = target();
    if (!t) return;
    cur = { ...t };
    indicator.style.opacity = '1';
    paint();
  }

  if (menu && dockLinks.length) {
    indicator = document.createElement('span');
    indicator.className = 'navbar__indicator';
    indicator.setAttribute('aria-hidden', 'true');
    menu.prepend(indicator);
    menu.classList.add('has-indicator');

    // Posição inicial sem animação, depois que as fontes definem as larguras
    snap();
    document.fonts?.ready.then(snap);
    window.addEventListener('resize', snap, { passive: true });
  }

  // ------------------------------------------------------------------
  // Clique: marca ativo, anima e só então navega
  // ------------------------------------------------------------------
  function bind(links, activate) {
    links.forEach(link => {
      link.addEventListener('click', (e) => {
        if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
        if (link.target === '_blank') return;

        const url = new URL(link.href, location.href);
        if (url.origin !== location.origin) return;
        if (url.pathname === location.pathname && url.search === location.search) return;

        if (reduced) return;

        e.preventDefault();
        activate(link);
        setTimeout(() => { location.href = url.href; }, NAV_DELAY);
      });
    });
  }

  bind(dockLinks, (link) => {
    dockLinks.forEach(l => l.classList.toggle('is-active', l === link));
    follow();
  });

  bind(bottomLinks, (link) => {
    bottomLinks.forEach(l => l.classList.toggle('is-active', l === link));
  });
})();
