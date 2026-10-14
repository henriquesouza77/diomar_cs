/**
 * navbar.js — Comportamento da navbar
 * - Aplica .is-scrolled ao rolar
 * - Controla o drawer mobile
 * - Controla o dropdown de usuário
 * - Busca contador de favoritos
 */
'use strict';

(() => {
  const navbar  = document.getElementById('navbar');
  const toggle  = document.getElementById('navbarToggle');
  const drawer  = document.getElementById('navbarDrawer');
  const overlay = document.getElementById('navbarOverlay');

  if (!navbar) return;

  // ============================================================
  // SCROLL STATE
  // ============================================================
  let ticking = false;

  function updateScrollState() {
    const y = window.scrollY;
    navbar.classList.toggle('is-scrolled', y > 20);
    ticking = false;
  }

  window.addEventListener('scroll', () => {
    if (!ticking) {
      requestAnimationFrame(updateScrollState);
      ticking = true;
    }
  }, { passive: true });

  updateScrollState();

  // ============================================================
  // DRAWER MOBILE
  // ============================================================
  function openDrawer() {
    navbar.classList.add('is-menu-open');
    toggle?.setAttribute('aria-expanded', 'true');
    toggle?.setAttribute('aria-label', 'Fechar menu');
    document.body.style.overflow = 'hidden';
  }

  function closeDrawer() {
    navbar.classList.remove('is-menu-open');
    toggle?.setAttribute('aria-expanded', 'false');
    toggle?.setAttribute('aria-label', 'Abrir menu');
    document.body.style.overflow = '';
  }

  toggle?.addEventListener('click', () => {
    navbar.classList.contains('is-menu-open') ? closeDrawer() : openDrawer();
  });

  overlay?.addEventListener('click', closeDrawer);

  drawer?.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', closeDrawer);
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && navbar.classList.contains('is-menu-open')) {
      closeDrawer();
    }
  });

  window.addEventListener('resize', () => {
    if (window.innerWidth >= 1024 && navbar.classList.contains('is-menu-open')) {
      closeDrawer();
    }
  });

  // ============================================================
  // USER DROPDOWN
  // ============================================================
  const userBtn = document.querySelector('.navbar__user-btn');
  const userDropdown = document.querySelector('.navbar__user');

  if (userBtn && userDropdown) {
    userBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      userDropdown.classList.toggle('is-open');
    });

    document.addEventListener('click', (e) => {
      if (!userDropdown.contains(e.target)) {
        userDropdown.classList.remove('is-open');
      }
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        userDropdown.classList.remove('is-open');
      }
    });
  }

  // ============================================================
  // SUBMENUS
  // ============================================================
  document.querySelectorAll('.navbar__item').forEach(item => {
    const link = item.querySelector('.navbar__link');
    const submenu = item.querySelector('.navbar__submenu');
    if (!submenu || !link) return;

    link.addEventListener('click', (e) => {
      if (window.innerWidth < 1024) {
        e.preventDefault();
        item.classList.toggle('is-open');
      }
    });

    if (window.matchMedia('(hover: hover)').matches) {
      item.addEventListener('mouseenter', () => item.classList.add('is-open'));
      item.addEventListener('mouseleave', () => item.classList.remove('is-open'));
    }
  });

  // ============================================================
  // FAVORITOS COUNT
  // ============================================================
  function updateFavCount(count) {
    const badge = document.getElementById('favCount');
    if (!badge) return;

    if (count > 0) {
      badge.textContent = count > 99 ? '99+' : String(count);
      badge.hidden = false;
    } else {
      badge.hidden = true;
    }
  }

  // Só busca favoritos se o usuário estiver logado
  // (evita erro 401/403 no console para visitantes)
  if (window.API && API.BASE_URL) {
    fetch(`${API.BASE_URL}/backend/api.php?route=auth/me`, {
      credentials: 'same-origin',
      headers: { 'Accept': 'application/json' }
    })
      .then(r => r.ok ? r.json() : null)
      .then(res => {
        if (res && res.data) {
          return API.favorites.list()
            .then(list => {
              updateFavCount(Array.isArray(list) ? list.length : 0);
            })
            .catch(() => {});
        }
      })
      .catch(() => {});
  }

  document.addEventListener('favorites:changed', (e) => {
    updateFavCount(e.detail?.count ?? 0);
  });
})();