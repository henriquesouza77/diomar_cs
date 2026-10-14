/**
 * favorites.js — Página de favoritos do usuário
 */
'use strict';

(() => {
  if (window.__PAGE__ !== 'favoritos') return;

  const $grid     = document.getElementById('favoritesGrid');
  const $skeleton = document.getElementById('favoritesSkeleton');
  const $empty    = document.getElementById('favoritesEmpty');

  async function load() {
    showSkeleton();

    try {
      const list = await API.favorites.list();

      if (!Array.isArray(list) || !list.length) {
        hideAll();
        $empty.hidden = false;
        return;
      }

      $grid.innerHTML = list.map(renderCard).join('');
      hideAll();
      $grid.hidden = false;

      bindRemove();
      requestAnimationFrame(() => Animations.refresh());
    } catch (err) {
      if (err.status === 401) {
        location.href = `${API.BASE_URL}/frontend/pages/login.php`;
        return;
      }
      console.error('[favorites]', err);
      App.toast('Erro ao carregar favoritos', 'error');
      hideAll();
      $empty.hidden = false;
    }
  }

  function renderCard(f) {
    const p = f.property || f;
    const img = p.main_image
      ? `${API.BASE_URL}/backend/uploads/properties/${p.main_image}`
      : `${API.BASE_URL}/frontend/images/placeholder.jpg`;

    return `
      <article class="property-card" data-fav-id="${p.id}">
        <a href="${API.BASE_URL}/frontend/pages/imovel.php?id=${p.id}" class="property-card__link">
          <div class="property-card__image">
            <img src="${img}" alt="${escapeHtml(p.title)}" loading="lazy">
            <button type="button" class="property-card__fav is-active" data-remove="${p.id}" aria-label="Remover dos favoritos">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
              </svg>
            </button>
          </div>
          <div class="property-card__body">
            <h3 class="property-card__title">${escapeHtml(p.title)}</h3>
            <p class="property-card__location">${escapeHtml((p.neighborhood || '') + (p.city ? ' — ' + p.city : ''))}</p>
            <div class="property-card__footer">
              <span class="property-card__price-value">R$ ${formatMoney(p.price)}</span>
            </div>
          </div>
        </a>
      </article>
    `;
  }

  function bindRemove() {
    document.querySelectorAll('[data-remove]').forEach(btn => {
      btn.addEventListener('click', async (e) => {
        e.preventDefault();
        e.stopPropagation();
        const id = btn.dataset.remove;

        try {
          await API.favorites.toggle(id);
          const card = btn.closest('.property-card');
          card.style.transition = 'opacity .3s, transform .3s';
          card.style.opacity = '0';
          card.style.transform = 'scale(.9)';
          setTimeout(() => {
            card.remove();
            if (!$grid.children.length) {
              hideAll();
              $empty.hidden = false;
            }
          }, 300);
          App.toast('Removido dos favoritos', 'success', 2000);
        } catch (err) {
          App.toast('Erro ao remover', 'error');
        }
      });
    });
  }

  function showSkeleton() {
    $skeleton.hidden = false;
    $grid.hidden = true;
    $empty.hidden = true;
  }

  function hideAll() {
    $skeleton.hidden = true;
    $grid.hidden = true;
    $empty.hidden = true;
  }

  function formatMoney(v) {
    return Number(v || 0).toLocaleString('pt-BR');
  }

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = String(str ?? '');
    return div.innerHTML;
  }

  load();
})();