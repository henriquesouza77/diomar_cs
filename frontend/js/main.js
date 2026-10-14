/**
 * main.js — Inicialização global da página
 * Carrega o que for comum a todas as páginas: loader, toast, dropdowns, modais.
 */
'use strict';

const App = (() => {

  // ============ PAGE LOADER ============
  function hidePageLoader() {
    const loader = document.getElementById('pageLoader');
    if (loader) {
      setTimeout(() => loader.classList.add('is-hidden'), 300);
    }
  }

  // ============ TOAST ============
  const toastContainer = (() => {
    let el = document.querySelector('.toast-container');
    if (!el) {
      el = document.createElement('div');
      el.className = 'toast-container';
      el.setAttribute('role', 'status');
      el.setAttribute('aria-live', 'polite');
      document.body.appendChild(el);
    }
    return el;
  })();

  function toast(message, type = 'info', duration = 3500) {
    const el = document.createElement('div');
    el.className = `toast toast-${type}`;
    el.innerHTML = `
      <span aria-hidden="true">${iconFor(type)}</span>
      <span>${escapeHtml(message)}</span>
    `;
    toastContainer.appendChild(el);

    setTimeout(() => {
      el.classList.add('is-leaving');
      el.addEventListener('animationend', () => el.remove(), { once: true });
    }, duration);
  }

  function iconFor(type) {
    const icons = {
      success: '✓',
      error:   '✕',
      warning: '⚠',
      info:    'ℹ',
    };
    return icons[type] || icons.info;
  }

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = String(str);
    return div.innerHTML;
  }

  // ============ DROPDOWNS ============
  function initDropdowns() {
    document.querySelectorAll('.dropdown').forEach(dd => {
      const trigger = dd.querySelector('[data-dropdown-trigger]');
      if (!trigger) return;

      trigger.addEventListener('click', (e) => {
        e.stopPropagation();
        document.querySelectorAll('.dropdown.is-open').forEach(o => {
          if (o !== dd) o.classList.remove('is-open');
        });
        dd.classList.toggle('is-open');
      });
    });

    document.addEventListener('click', () => {
      document.querySelectorAll('.dropdown.is-open').forEach(d => d.classList.remove('is-open'));
    });
  }

  // ============ MODAIS ============
  function initModals() {
    document.querySelectorAll('[data-modal-open]').forEach(btn => {
      btn.addEventListener('click', () => {
        const id = btn.dataset.modalOpen;
        const modal = document.getElementById(id);
        if (modal) openModal(modal);
      });
    });

    document.querySelectorAll('.modal-overlay').forEach(overlay => {
      overlay.addEventListener('click', (e) => {
        if (e.target === overlay) closeModal(overlay);
      });

      const closeBtn = overlay.querySelector('.modal__close');
      if (closeBtn) closeBtn.addEventListener('click', () => closeModal(overlay));
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        const open = document.querySelector('.modal-overlay.is-open');
        if (open) closeModal(open);
      }
    });
  }

  function openModal(modal) {
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    const focusable = modal.querySelector('input, select, textarea, button');
    if (focusable) setTimeout(() => focusable.focus(), 100);
  }

  function closeModal(modal) {
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  // ============ TABS ============
  function initTabs() {
    document.querySelectorAll('.tabs').forEach(tabs => {
      const items = tabs.querySelectorAll('.tabs__item');
      const slider = tabs.querySelector('.tabs__slider');

      const moveSlider = (activeItem) => {
        if (!slider) return;
        const tabsRect = tabs.getBoundingClientRect();
        const itemRect = activeItem.getBoundingClientRect();
        slider.style.transform = `translateX(${itemRect.left - tabsRect.left - 4}px)`;
        slider.style.width = `${itemRect.width}px`;
      };

      items.forEach(item => {
        item.addEventListener('click', () => {
          items.forEach(i => {
            i.classList.remove('is-active');
            i.setAttribute('aria-selected', 'false');
          });
          item.classList.add('is-active');
          item.setAttribute('aria-selected', 'true');
          moveSlider(item);
        });
      });

      const initial = tabs.querySelector('.tabs__item.is-active');
      if (initial) {
        requestAnimationFrame(() => moveSlider(initial));
      }
    });
  }

  // ============ ACTIVE NAV LINK ============
  function markActiveNav() {
    const current = window.location.pathname;
    document.querySelectorAll('.navbar__link, .navbar__drawer-link').forEach(link => {
      const href = link.getAttribute('href');
      if (!href) return;
      const clean = href.split('?')[0];
      if (current.includes(clean) && clean !== '/') {
        link.classList.add('is-active');
      }
    });
  }

  // ============ SMOOTH SCROLL ============
  function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(link => {
      link.addEventListener('click', (e) => {
        const targetId = link.getAttribute('href');
        if (targetId === '#' || targetId.length < 2) return;
        const target = document.querySelector(targetId);
        if (!target) return;
        e.preventDefault();
        const offset = 80;
        const top = target.getBoundingClientRect().top + window.pageYOffset - offset;
        window.scrollTo({ top, behavior: 'smooth' });
      });
    });
  }

  // ============ RIPPLE ============
  function initRipple() {
    document.querySelectorAll('.btn-primary, .btn-white, .btn-whatsapp').forEach(btn => {
      btn.classList.add('ripple');
      btn.addEventListener('click', function (e) {
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;

        const wave = document.createElement('span');
        wave.className = 'ripple__wave';
        wave.style.width = wave.style.height = size + 'px';
        wave.style.left = x + 'px';
        wave.style.top = y + 'px';

        this.appendChild(wave);
        wave.addEventListener('animationend', () => wave.remove());
      });
    });
  }

  // ============ INIT ============
  function init() {
    try {
      hidePageLoader();
      initDropdowns();
      initModals();
      initTabs();
      markActiveNav();
      initSmoothScroll();
      initRipple();
    } catch (err) {
      console.warn('[App] Erro na inicialização:', err);
    }
  }

  // Auto-inicialização robusta
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  // Fallback do loader
  window.addEventListener('load', hidePageLoader);

  // Fallback de segurança: força visibilidade de animações após 1.5s
  window.addEventListener('load', () => {
    setTimeout(() => {
      document.querySelectorAll('[data-reveal]:not(.is-visible), [data-stagger]:not(.is-visible)').forEach(el => {
        el.classList.add('is-visible');
      });
    }, 1500);
  });

  return { toast, openModal, closeModal };
})();

  // ================================================================
  // FALLBACK — Se o cursor follower falhar, restaura o cursor nativo
  // ================================================================
  window.addEventListener('load', () => {
    setTimeout(() => {
      const hasFollower = document.querySelector('.cursor-follower');
      // Se o follower não foi criado (JS falhou), garante cursor nativo
      if (!hasFollower) {
        document.body.classList.remove('cursor-active');
      }
    }, 1000);
  });

window.App = App;