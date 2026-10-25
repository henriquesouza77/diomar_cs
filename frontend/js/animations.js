/**
 * animations.js — Sistema completo de animações
 * - Scroll reveal via IntersectionObserver
 * - Parallax
 * - Contadores animados
 * - Cursor follower
 * - Magnetic buttons
 * - Tilt 3D
 * - Respeita prefers-reduced-motion
 * - Fallback automático se algo falhar
 */
'use strict';

const Animations = (() => {

  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const isTouch = !window.matchMedia('(hover: hover)').matches;

  // =========================================================================
  // SCROLL REVEAL
  // =========================================================================
  // Fade-in ao entrar e fade-out ao sair: o observador usa uma "faixa de leitura"
  // (a viewport encolhida em cima e embaixo), então o bloco some e volta enquanto
  // ainda está parcialmente visível. Reaplicável: Animations.refresh() observa só o que é novo.
  const revealSeen = new WeakSet();
  let revealObserver = null;
  let revealBottomBound = false;

  function initReveal() {
    const elements = [...document.querySelectorAll(
      '[data-reveal], [data-stagger], .img-reveal, .text-reveal'
    )].filter(el => !revealSeen.has(el));
    if (!elements.length) return;
    elements.forEach(el => revealSeen.add(el));

    if (prefersReduced || !('IntersectionObserver' in window)) {
      elements.forEach(el => el.classList.add('is-visible'));
      return;
    }

    revealObserver = revealObserver || new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        const el = entry.target;
        // Cortina da imagem e texto por linha só abrem uma vez: fechar de novo piscaria nos cards
        const oneShot = el.matches('.img-reveal, .text-reveal');
        if (entry.isIntersecting) {
          el.classList.add('is-visible');
          if (oneShot) revealObserver.unobserve(el);
        } else if (!oneShot) {
          el.classList.remove('is-visible');
        }
      });
    }, { threshold: 0, rootMargin: '-12% 0px -60px 0px' });

    elements.forEach(el => revealObserver.observe(el));

    // Fim da página (ou página curta, sem rolagem): o rodapé nunca chega na faixa de leitura,
    // então revela o que está na tela
    if (!revealBottomBound) {
      revealBottomBound = true;
      const revealAtBottom = () => {
        if (window.scrollY + window.innerHeight < document.documentElement.scrollHeight - 4) return;
        document.querySelectorAll('[data-reveal], [data-stagger]').forEach(el => {
          const r = el.getBoundingClientRect();
          if (r.top < window.innerHeight && r.bottom > 0) el.classList.add('is-visible');
        });
      };
      window.addEventListener('scroll', revealAtBottom, { passive: true });
      requestAnimationFrame(revealAtBottom);
    }
  }

  // =========================================================================
  // PARALLAX
  // =========================================================================
  function initParallax() {
    if (prefersReduced) return;

    const elements = document.querySelectorAll('[data-parallax]');
    if (!elements.length) return;

    let ticking = false;

    function update() {
      const scrollY = window.scrollY;
      const vh = window.innerHeight;

      elements.forEach(el => {
        const rect = el.getBoundingClientRect();
        if (rect.bottom < -200 || rect.top > vh + 200) return;

        const speed = parseFloat(el.dataset.parallax) || 0.3;
        const offset = (scrollY - el.offsetTop + vh) * speed;
        el.style.transform = `translate3d(0, ${offset}px, 0)`;
      });

      ticking = false;
    }

    window.addEventListener('scroll', () => {
      if (!ticking) {
        requestAnimationFrame(update);
        ticking = true;
      }
    }, { passive: true });

    update();
  }

  // =========================================================================
  // CONTADORES
  // =========================================================================
  function initCounters() {
    const counters = document.querySelectorAll('[data-counter]');
    if (!counters.length) return;

    if (prefersReduced || !('IntersectionObserver' in window)) {
      counters.forEach(el => {
        el.textContent = Number(el.dataset.counter).toLocaleString('pt-BR');
      });
      return;
    }

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (!entry.isIntersecting) return;
        animateCounter(entry.target);
        observer.unobserve(entry.target);
      });
    }, { threshold: 0.5 });

    counters.forEach(el => observer.observe(el));
  }

  function animateCounter(el) {
    const target = parseInt(el.dataset.counter, 10);
    if (isNaN(target)) return;

    const duration = 1600;
    const start = performance.now();

    function step(now) {
      const elapsed = now - start;
      const progress = Math.min(elapsed / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 4);
      const current = Math.floor(eased * target);

      el.textContent = current.toLocaleString('pt-BR');

      if (progress < 1) requestAnimationFrame(step);
      else el.textContent = target.toLocaleString('pt-BR');
    }

    requestAnimationFrame(step);
  }

  // =========================================================================
  // CURSOR FOLLOWER PREMIUM — Círculo com lag + ponto rápido
  // =========================================================================
  function initCursorFollower() {
    if (prefersReduced || isTouch) return;

    // Cria os dois elementos
    const follower = document.createElement('div');
    follower.className = 'cursor-follower';
    follower.setAttribute('aria-hidden', 'true');

    const dot = document.createElement('div');
    dot.className = 'cursor-dot';
    dot.setAttribute('aria-hidden', 'true');

    document.body.appendChild(follower);
    document.body.appendChild(dot);

    let mouseX = 0;
    let mouseY = 0;
    let followerX = 0;
    let followerY = 0;
    let dotX = 0;
    let dotY = 0;
    let isMoving = false;

    // Detecta movimento do mouse
    document.addEventListener('mousemove', (e) => {
      mouseX = e.clientX;
      mouseY = e.clientY;

      if (!isMoving) {
        isMoving = true;
        follower.classList.add('is-active');
        dot.classList.add('is-active');
        document.body.classList.add('cursor-active');
      }
    });

    // Esconde ao sair da janela
    document.addEventListener('mouseleave', () => {
      isMoving = false;
      follower.classList.remove('is-active');
      dot.classList.remove('is-active');
      document.body.classList.remove('cursor-active');
    });

    // Animação em loop
    function loop() {
      // Follower — lag suave
      followerX += (mouseX - followerX) * 0.15;
      followerY += (mouseY - followerY) * 0.15;
      follower.style.transform = `translate3d(${followerX}px, ${followerY}px, 0) translate(-50%, -50%)`;

      // Dot — resposta mais rápida
      dotX += (mouseX - dotX) * 0.35;
      dotY += (mouseY - dotY) * 0.35;
      dot.style.transform = `translate3d(${dotX}px, ${dotY}px, 0) translate(-50%, -50%)`;

      requestAnimationFrame(loop);
    }
    loop();

    // Expande em elementos interativos
    const hoverTargets = 'a, button, [data-cursor="hover"], .property-card, .category-card, .btn, [role="button"]';

    document.addEventListener('mouseover', (e) => {
      if (e.target.closest(hoverTargets)) {
        follower.classList.add('is-hovering');
        dot.classList.add('is-hovering');
      }
    });

    document.addEventListener('mouseout', (e) => {
      if (e.target.closest(hoverTargets)) {
        follower.classList.remove('is-hovering');
        dot.classList.remove('is-hovering');
      }
    });
  }

  // =========================================================================
  // MAGNETIC BUTTONS
  // =========================================================================
  function initMagneticButtons() {
    if (prefersReduced || isTouch) return;

    const buttons = document.querySelectorAll('.btn-magnetic');
    if (!buttons.length) return;

    buttons.forEach(btn => {
      let rafId = null;
      let targetX = 0;
      let targetY = 0;
      let currentX = 0;
      let currentY = 0;

      btn.addEventListener('mousemove', (e) => {
        const rect = btn.getBoundingClientRect();
        const x = e.clientX - rect.left - rect.width / 2;
        const y = e.clientY - rect.top - rect.height / 2;

        targetX = x * 0.25;
        targetY = y * 0.25;

        if (!rafId) rafId = requestAnimationFrame(update);
      });

      btn.addEventListener('mouseleave', () => {
        targetX = 0;
        targetY = 0;
      });

      function update() {
        currentX += (targetX - currentX) * 0.15;
        currentY += (targetY - currentY) * 0.15;
        btn.style.transform = `translate3d(${currentX}px, ${currentY}px, 0)`;

        if (Math.abs(targetX - currentX) > 0.1 || Math.abs(targetY - currentY) > 0.1) {
          rafId = requestAnimationFrame(update);
        } else {
          rafId = null;
        }
      }
    });
  }

  // =========================================================================
  // TILT 3D
  // =========================================================================
  function initTilt() {
    if (prefersReduced || isTouch) return;

    const elements = document.querySelectorAll('[data-tilt]');
    if (!elements.length) return;

    elements.forEach(el => {
      const max = el.dataset.tilt === 'strong' ? 10 : el.dataset.tilt === 'soft' ? 4 : 6;

      el.addEventListener('mousemove', (e) => {
        const rect = el.getBoundingClientRect();
        const x = (e.clientX - rect.left) / rect.width - 0.5;
        const y = (e.clientY - rect.top) / rect.height - 0.5;

        el.style.transform = `perspective(1000px) rotateY(${x * max}deg) rotateX(${-y * max}deg)`;
      });

      el.addEventListener('mouseleave', () => {
        el.style.transform = '';
      });
    });
  }

  // =========================================================================
  // PAGE LOADER
  // =========================================================================
  function initLoader() {
    const loader = document.getElementById('pageLoader');
    if (!loader) return;

    window.addEventListener('load', () => {
      setTimeout(() => loader.classList.add('is-hidden'), 200);
    });

    // Fallback caso 'load' já tenha disparado
    if (document.readyState === 'complete') {
      setTimeout(() => loader.classList.add('is-hidden'), 200);
    }
  }

  // =========================================================================
  // REFRESH
  // =========================================================================
  function refresh() {
    initReveal();
    initCounters();
  }

  // =========================================================================
  // INIT
  // =========================================================================
  function init() {
    try {
      initReveal();
      initParallax();
      initCounters();
      initCursorFollower();
      initMagneticButtons();
      initTilt();
      initLoader();
    } catch (err) {
      console.warn('[Animations] Erro:', err);
      document
        .querySelectorAll('[data-reveal], [data-stagger]')
        .forEach(el => el.classList.add('is-visible'));
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  return { init, refresh };
})();

window.Animations = Animations;