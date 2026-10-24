/**
 * properties.js — Página de listagem de imóveis
 * - Aplica filtros da URL aos controles
 * - Busca via API
 * - Renderiza cards
 * - Paginação
 * - Ordenação
 * - Estado vazio / skeleton / erros
 */
'use strict';

(() => {
  if (window.__HOME__) {
    initFeaturedProperties();
    return;
  }

  if (window.__PAGE__ !== 'imoveis') return;

  const $grid      = document.getElementById('propertiesGrid');
  const $skeleton  = document.getElementById('propertiesSkeleton');
  const $count     = document.getElementById('toolbarCount');
  const $result    = document.getElementById('resultCount');
  const $pagination= document.getElementById('paginationContainer');
  const $active    = document.getElementById('activeFilters');
  const $clearBtn  = document.getElementById('clearFilters');

  const state = {
    page: 1,
    per_page: 12,
    purpose: '',
    city: '',
    neighborhood: '',
    type: '',
    min_price: '',
    max_price: '',
    bedrooms: '',
    suites: '',
    bathrooms: '',
    parking_spaces: '',
    min_area: '',
    sort: 'recent',
  };

  // ============ LEITURA DA URL ============
  function readUrl() {
    const params = new URLSearchParams(window.location.search);
    Object.keys(state).forEach(key => {
      if (params.has(key) && key !== 'page' && key !== 'per_page') {
        state[key] = params.get(key);
      }
    });
    if (params.has('page')) state.page = parseInt(params.get('page'), 10) || 1;
  }

  // ============ APLICA ESTADO AOS CONTROLES ============
  function applyStateToControls() {
    if (state.purpose) {
      const tab = document.querySelector(`#purposeTabs [data-purpose="${state.purpose}"]`);
      if (tab) tab.click();
    }
    setInput('filterCity', state.city);
    setInput('filterNeighborhood', state.neighborhood);
    setSelect('filterType', state.type);
    setInput('filterMinPrice', state.min_price);
    setInput('filterMaxPrice', state.max_price);
    setCounter('#filterBedrooms', state.bedrooms);
    setCounter('#filterSuites', state.suites);
    setCounter('#filterBathrooms', state.bathrooms);
    setCounter('#filterParking', state.parking_spaces);
    setInput('filterMinArea', state.min_area);
    setSelect('sortBy', state.sort);
  }

  function setInput(id, value) {
    const el = document.getElementById(id);
    if (el && value) el.value = value;
  }

  function setSelect(id, value) {
    const el = document.getElementById(id);
    if (el && value) el.value = value;
  }

  function setCounter(selector, value) {
    if (!value) return;
    const container = document.querySelector(selector);
    if (!container) return;
    const btn = container.querySelector(`[data-value="${value}"]`);
    if (btn) {
      container.querySelectorAll('.counter-btn').forEach(b => b.classList.remove('is-active'));
      btn.classList.add('is-active');
    }
  }

  // ============ BINDINGS ============
  function bindControls() {
    // Tabs purpose
    document.querySelectorAll('#purposeTabs .tabs__item').forEach(btn => {
      btn.addEventListener('click', () => {
        state.purpose = btn.dataset.purpose || '';
        state.page = 1;
        search();
      });
    });

    // Inputs de texto com debounce
    const debounced = debounce(() => { state.page = 1; search(); }, 500);
    document.getElementById('filterCity')?.addEventListener('input', (e) => {
      state.city = e.target.value.trim(); debounced();
    });
    document.getElementById('filterNeighborhood')?.addEventListener('input', (e) => {
      state.neighborhood = e.target.value.trim(); debounced();
    });
    document.getElementById('filterMinPrice')?.addEventListener('input', (e) => {
      e.target.value = maskPrice(e.target.value);
      state.min_price = e.target.value.replace(/\D/g, ''); debounced();
    });
    document.getElementById('filterMaxPrice')?.addEventListener('input', (e) => {
      e.target.value = maskPrice(e.target.value);
      state.max_price = e.target.value.replace(/\D/g, ''); debounced();
    });
    document.getElementById('filterMinArea')?.addEventListener('input', (e) => {
      e.target.value = e.target.value.replace(/\D/g, '');
      state.min_area = e.target.value; debounced();
    });

    // Selects
    document.getElementById('filterType')?.addEventListener('change', (e) => {
      state.type = e.target.value; state.page = 1; search();
    });
    document.getElementById('sortBy')?.addEventListener('change', (e) => {
      state.sort = e.target.value; search();
    });

    // Counters
    ['filterBedrooms:bedrooms', 'filterSuites:suites', 'filterBathrooms:bathrooms', 'filterParking:parking_spaces']
      .forEach(pair => {
        const [containerId, stateKey] = pair.split(':');
        const container = document.getElementById(containerId);
        if (!container) return;
        container.querySelectorAll('.counter-btn').forEach(btn => {
          btn.addEventListener('click', () => {
            container.querySelectorAll('.counter-btn').forEach(b => b.classList.remove('is-active'));
            btn.classList.add('is-active');
            state[stateKey] = btn.dataset.value || '';
            state.page = 1;
            search();
          });
        });
      });

    // Limpar
    $clearBtn?.addEventListener('click', clearFilters);

    // View switch
    document.querySelectorAll('.view-switch__btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.view-switch__btn').forEach(b => b.classList.remove('is-active'));
        btn.classList.add('is-active');
        $grid.style.gridTemplateColumns = btn.dataset.view === 'list'
          ? '1fr'
          : '';
      });
    });
  }

  // ============ BUSCA ============
  async function search() {
    showSkeleton();
    updateUrl();

    const params = { ...state };
    Object.keys(params).forEach(k => {
      if (params[k] === '' || params[k] === null) delete params[k];
    });

    try {
      const data = await API.properties.list(params);
      render(data);
    } catch (err) {
      console.error('[properties]', err);
      renderError(err.message);
    }
  }

  // ============ RENDER ============
  function render(data) {
    hideSkeleton();

    const items = data.items || [];
    const total = data.total || 0;
    const pages = data.pages || 1;

    // Toolbar
    if ($count) $count.innerHTML = `<strong>${total}</strong> imóve${total === 1 ? 'l' : 'is'} encontrado${total === 1 ? '' : 's'}`;
    if ($result) $result.textContent = `${total} imóve${total === 1 ? 'l' : 'is'} disponíve${total === 1 ? 'l' : 'is'}`;

    // Grid
    if (!items.length) {
      renderEmpty();
      $pagination.innerHTML = '';
      return;
    }

    $grid.innerHTML = items.map(renderCard).join('');
    bindFavoriteButtons();

    // Paginação
    renderPagination(pages);

    // Chama animações para os novos cards
    requestAnimationFrame(() => Animations.refresh());
  }

  function renderCard(p) {
    const base = API.BASE_URL;
    const img = p.main_image
      ? `${base}/backend/uploads/properties/${p.main_image}`
      : `${base}/frontend/images/placeholder.jpg`;

    const isNew = p.created_at && (Date.now() - new Date(p.created_at).getTime()) < 15 * 86400000;
    const priceLabel = p.purpose === 'aluguel' ? 'Aluguel mensal' : 'Valor de venda';
    const price = Number(p.price).toLocaleString('pt-BR');

    return `
      <article class="property-card" data-reveal data-property-id="${p.id}">
        <a href="${base}/frontend/pages/imovel.php?id=${p.id}" class="property-card__link">
          <div class="property-card__image img-reveal">
            <img src="${img}" alt="${escapeHtml(p.title)}" loading="lazy" decoding="async">
            <div class="property-card__badges">
              ${p.featured ? '<span class="property-card__badge property-card__badge--featured">Destaque</span>' : ''}
              <span class="property-card__badge">${p.purpose === 'venda' ? 'Venda' : 'Aluguel'}</span>
              ${isNew ? '<span class="property-card__badge property-card__badge--new">Novo</span>' : ''}
            </div>
            <button type="button" class="property-card__fav" data-fav-id="${p.id}" aria-label="Favoritar">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
              </svg>
            </button>
          </div>
          <div class="property-card__body">
            <h3 class="property-card__title">${escapeHtml(p.title)}</h3>
            <p class="property-card__location">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
              ${escapeHtml((p.neighborhood || '') + (p.city ? ' — ' + p.city : ''))}
            </p>
            <ul class="property-card__meta">
              ${p.bedrooms ? `<li class="property-card__meta-item">${p.bedrooms} quartos</li>` : ''}
              ${p.bathrooms ? `<li class="property-card__meta-item">${p.bathrooms} banh.</li>` : ''}
              ${p.parking_spaces ? `<li class="property-card__meta-item">${p.parking_spaces} vagas</li>` : ''}
              ${p.area_built ? `<li class="property-card__meta-item">${p.area_built} m²</li>` : ''}
            </ul>
            <div class="property-card__footer">
              <div class="property-card__price">
                <span class="property-card__price-label">${priceLabel}</span>
                <span class="property-card__price-value">R$ ${price}${p.purpose === 'aluguel' ? '<small>/mês</small>' : ''}</span>
              </div>
              <span class="property-card__code">#${escapeHtml(p.code)}</span>
            </div>
          </div>
        </a>
      </article>
    `;
  }

  function renderEmpty() {
    $grid.innerHTML = `
      <div class="empty-state" style="grid-column:1/-1;">
        <div class="empty-state__icon">🔍</div>
        <h3 class="empty-state__title">Nenhum imóvel encontrado</h3>
        <p class="empty-state__text">Tente ajustar os filtros ou volte mais tarde.</p>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('clearFilters').click()">Limpar filtros</button>
      </div>
    `;
  }

  function renderError(message) {
    $grid.innerHTML = `
      <div class="empty-state" style="grid-column:1/-1;">
        <div class="empty-state__icon">⚠️</div>
        <h3 class="empty-state__title">Erro ao carregar imóveis</h3>
        <p class="empty-state__text">${escapeHtml(message || 'Verifique sua conexão e tente novamente.')}</p>
        <button type="button" class="btn btn-primary" onclick="location.reload()">Tentar novamente</button>
      </div>
    `;
  }

  function renderPagination(totalPages) {
    if (totalPages <= 1) { $pagination.innerHTML = ''; return; }

    const current = state.page;
    const range = 2;
    const start = Math.max(1, current - range);
    const end   = Math.min(totalPages, current + range);

    let html = '<nav class="pagination" aria-label="Paginação">';
    html += `<button class="pagination__item" data-page="${current - 1}" ${current <= 1 ? 'disabled' : ''}>‹</button>`;

    if (start > 1) {
      html += `<button class="pagination__item" data-page="1">1</button>`;
      if (start > 2) html += `<span class="pagination__item" aria-disabled="true">…</span>`;
    }

    for (let i = start; i <= end; i++) {
      html += `<button class="pagination__item${i === current ? ' is-active' : ''}" data-page="${i}">${i}</button>`;
    }

    if (end < totalPages) {
      if (end < totalPages - 1) html += `<span class="pagination__item" aria-disabled="true">…</span>`;
      html += `<button class="pagination__item" data-page="${totalPages}">${totalPages}</button>`;
    }

    html += `<button class="pagination__item" data-page="${current + 1}" ${current >= totalPages ? 'disabled' : ''}>›</button>`;
    html += '</nav>';

    $pagination.innerHTML = html;

    $pagination.querySelectorAll('[data-page]').forEach(btn => {
      btn.addEventListener('click', () => {
        state.page = parseInt(btn.dataset.page, 10);
        search();
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    });
  }

  // ============ SKELETON ============
  function showSkeleton() {
    $grid.hidden = true;
    $skeleton.hidden = false;
  }

  function hideSkeleton() {
    $grid.hidden = false;
    $skeleton.hidden = true;
  }

  // ============ URL ============
  function updateUrl() {
    const params = new URLSearchParams();
    Object.entries(state).forEach(([k, v]) => {
      if (v !== '' && v !== null && v !== undefined) params.set(k, v);
    });
    const newUrl = `${window.location.pathname}?${params.toString()}`;
    history.replaceState(null, '', newUrl);
  }

  // ============ FAVORITOS ============
  function bindFavoriteButtons() {
    document.querySelectorAll('[data-fav-id]').forEach(btn => {
      btn.addEventListener('click', async (e) => {
        e.preventDefault();
        e.stopPropagation();
        const id = btn.dataset.favId;

        try {
          const res = await API.favorites.toggle(id);
          btn.classList.toggle('is-active', res.favorited);
          App.toast(res.favorited ? 'Adicionado aos favoritos' : 'Removido dos favoritos', 'success', 2000);

          document.dispatchEvent(new CustomEvent('favorites:changed', {
            detail: { count: res.total }
          }));
        } catch (err) {
          if (err.status === 401) {
            App.toast('Faça login para salvar favoritos', 'warning');
            setTimeout(() => location.href = `${API.BASE_URL}/frontend/pages/login.php`, 900);
          } else {
            App.toast('Erro ao favoritar', 'error');
          }
        }
      });
    });
  }

  async function initFeaturedProperties() {
    const grid = document.getElementById('featuredGrid');
    const skeleton = document.getElementById('featuredSkeleton');
    if (!grid || !skeleton) return;

    skeleton.hidden = false;
    grid.hidden = true;

    try {
      const result = await API.properties.list({ featured: 1, page: 1 });
      const items = Array.isArray(result?.items) ? result.items.slice(0, 3) : [];

      if (!items.length) {
        grid.innerHTML = '<p class="empty-state__text">Não há imóveis em destaque no momento.</p>';
      } else {
        grid.innerHTML = items.map(renderCard).join('');
        bindFavoriteButtons();
        requestAnimationFrame(() => Animations.refresh());
      }
      grid.hidden = false;
    } catch (error) {
      console.error('[featured-properties]', error);
      grid.innerHTML = '<p class="empty-state__text">Não foi possível carregar os imóveis em destaque.</p>';
      grid.hidden = false;
    } finally {
      skeleton.hidden = true;
    }
  }

  // ============ LIMPAR ============
  function clearFilters() {
    Object.keys(state).forEach(k => {
      if (k === 'page') state[k] = 1;
      else if (k === 'per_page') return;
      else if (k === 'sort') state[k] = 'recent';
      else state[k] = '';
    });

    document.querySelectorAll('.filters input').forEach(i => i.value = '');
    document.querySelectorAll('.filters select').forEach(s => s.selectedIndex = 0);
    document.querySelectorAll('.counter-btn').forEach(b => b.classList.remove('is-active'));
    document.getElementById('purposeTabs')?.querySelectorAll('.tabs__item').forEach(t => {
      t.classList.toggle('is-active', t.dataset.purpose === '');
    });

    search();
  }

  // ============ HELPERS ============
  function debounce(fn, ms) {
    let t;
    return (...args) => {
      clearTimeout(t);
      t = setTimeout(() => fn(...args), ms);
    };
  }

  function maskPrice(value) {
    const digits = String(value).replace(/\D/g, '');
    if (!digits) return '';
    return Number(digits).toLocaleString('pt-BR');
  }

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = String(str ?? '');
    return div.innerHTML;
  }

  // ============ INIT ============
  function init() {
    readUrl();
    applyStateToControls();
    bindControls();
    search();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();