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
  // USER DROPDOWN (Delegação de eventos - sempre funciona)
  // ============================================================
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.navbar__user-btn');
    
    if (btn) {
      e.preventDefault();
      e.stopPropagation();
      const dropdown = btn.closest('.navbar__user');
      if (dropdown) {
        dropdown.classList.toggle('is-open');
      }
      return;
    }

    if (!e.target.closest('.navbar__user')) {
      document.querySelectorAll('.navbar__user').forEach(d => d.classList.remove('is-open'));
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

  if (window.API && API.BASE_URL) {
    fetch(`${API.BASE_URL}/backend/api/index.php?route=auth/me`, {
      credentials: 'same-origin',
      headers: { 'Accept': 'application/json' }
    })
      .then(r => r.json().catch(() => null))   // sempre lê o corpo (inclusive no 401): corpo não lido deixa a requisição pendurada
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