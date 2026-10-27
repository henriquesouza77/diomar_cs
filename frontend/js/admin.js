/**
 * admin.js — Painel administrativo DIOMAR_CS
 * Módulos: dashboard, formulário, upload de fotos, leads
 */
'use strict';

(() => {
  const section = window.__ADMIN__;
  if (!section) {
    console.warn('[admin.js] window.__ADMIN__ não definido — abortando.');
    return;
  }

  // ============================================================
  // HELPERS
  // ============================================================
  const money = (v) => Number(v || 0).toLocaleString('pt-BR');

  const esc = (str) => {
    const div = document.createElement('div');
    div.textContent = String(str ?? '');
    return div.innerHTML;
  };

  const debounce = (fn, ms) => {
    let t;
    return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
  };

  const toast = (msg, type = 'info') => {
    if (window.App && window.App.toast) return window.App.toast(msg, type);
    alert(msg);
  };

  const confirmDialog = (msg) => window.confirm(msg);

  const UPLOAD_URL = (API.BASE_URL || '') + '/backend/uploads/properties/';
  const PLACEHOLDER = (API.BASE_URL || '') + '/frontend/images/placeholder.svg';

  const imageUrl = (filename) =>
    filename && filename !== 'placeholder.svg' ? UPLOAD_URL + filename : PLACEHOLDER;

  // ============================================================
  // SIDEBAR MOBILE
  // ============================================================
  const toggle  = document.getElementById('adminSidebarToggle');
  const sidebar = document.getElementById('adminSidebar');
  const overlay = document.getElementById('adminSidebarOverlay');

  toggle?.addEventListener('click', () => {
    const isOpen = sidebar?.classList.toggle('is-open') ?? false;
    overlay?.classList.toggle('is-open', isOpen);
    toggle.setAttribute('aria-expanded', String(isOpen));
  });

  overlay?.addEventListener('click', () => {
    sidebar?.classList.remove('is-open');
    overlay?.classList.remove('is-open');
    toggle?.setAttribute('aria-expanded', 'false');
  });

  // ============================================================
  // DASHBOARD
  // ============================================================
  function initDashboard() {
    loadStats();
    loadRecent();
  }

  async function loadStats() {
    try {
      const stats = await API.dashboard.stats();

      setStat('total_properties', stats.total_properties);
      setStat('available',        stats.available);
      setStat('leads_today',      stats.leads_today);
      setStat('active_agents',    stats.active_agents);

      const commissionEl = document.querySelector('[data-stat="total_commission"]');
      if (commissionEl) commissionEl.textContent = 'R$ ' + money(stats.total_commission || 0);

      const soldValueEl = document.querySelector('[data-stat="sold_total_value"]');
      if (soldValueEl) soldValueEl.textContent = 'R$ ' + money(stats.sold_total_value || 0);
    } catch (err) {
      console.warn('[dashboard stats]', err);
    }
  }

  function setStat(key, value) {
    const el = document.querySelector(`[data-stat="${key}"]`);
    if (el) animateNumber(el, Number(value || 0));
  }

  function animateNumber(el, target) {
    const duration = 900;
    const start = performance.now();
    function step(now) {
      const p = Math.min((now - start) / duration, 1);
      const eased = 1 - Math.pow(1 - p, 3);
      el.textContent = Math.floor(eased * target).toLocaleString('pt-BR');
      if (p < 1) requestAnimationFrame(step);
      else el.textContent = target.toLocaleString('pt-BR');
    }
    requestAnimationFrame(step);
  }

  async function loadRecent() {
    const tbody = document.getElementById('recentProperties');
    if (!tbody) return;

    try {
      const data = await API.dashboard.recentProperties();
      const items = data.items || data || [];

      if (!items.length) {
        tbody.innerHTML = `
          <tr><td colspan="5">
            <div class="admin-empty">
              <div class="admin-empty__icon">🏠</div>
              <div class="admin-empty__title">Nenhum imóvel cadastrado</div>
              <p class="admin-empty__text">Comece adicionando seu primeiro imóvel.</p>
              <a href="${API.BASE_URL}/frontend/pages/admin/imovel-form.php" class="btn btn-primary">
                Adicionar imóvel
              </a>
            </div>
          </td></tr>
        `;
        return;
      }

      tbody.innerHTML = items.map(p => `
        <tr>
          <td><img src="${imageUrl(p.main_image)}" class="admin-table__thumb" alt="" loading="lazy"></td>
          <td><span class="admin-table__title">${esc(p.title)}</span><span class="admin-table__meta">#${esc(p.code)}</span></td>
          <td>${esc((p.neighborhood || '') + (p.city ? ' — ' + p.city : ''))}</td>
          <td>${esc(p.status)}</td>
          <td>
            <div class="admin-table__actions">
              <a href="${API.BASE_URL}/frontend/pages/admin/imovel-form.php?id=${p.id}" class="admin-action-btn">✏️</a>
            </div>
          </td>
        </tr>
      `).join('');
    } catch (err) {
      console.warn('[recent]', err);
    }
  }

  // ============================================================
  // FORMULÁRIO DE IMÓVEL
  // ============================================================
  function initImovelForm() {
    console.log('[admin.js] initImovelForm iniciado');

    const form = document.getElementById('propertyForm');
    if (!form) {
      console.warn('[admin.js] propertyForm não encontrado');
      return;
    }

    const propertyId = parseInt(form.dataset.propertyId || '0', 10);
    console.log('[admin.js] propertyId:', propertyId);

    // Estado compartilhado do uploader
    const uploaderState = {
      localFiles: [],   // File objects de fotos novas (não enviadas)
      isSubmitting: false,
    };

    if (propertyId > 0) loadPropertyForEdit(propertyId);

    loadCategoriesAndFeatures();
    setupImageUploader(propertyId, uploaderState);

    // Máscaras de número
    document.querySelectorAll('input[inputmode="numeric"]').forEach(input => {
      if (input.type === 'text' && !input.dataset.noMask) {
        input.addEventListener('input', (e) => {
          const digits = e.target.value.replace(/\D/g, '');
          e.target.value = digits ? Number(digits).toLocaleString('pt-BR') : '';
        });
      }
    });

    // Submit — chama diretamente o backend
    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      if (uploaderState.isSubmitting) return;
      uploaderState.isSubmitting = true;

      const btn = form.querySelector('button[type="submit"]');
      const original = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = 'Salvando...';

      const formData = new FormData(form);
      formData.set('published', form.querySelector('[name="published"]').checked ? '1' : '0');
      ['price', 'condo_fee', 'iptu'].forEach(k => {
        const v = formData.get(k);
        if (v) formData.set(k, String(v).replace(/\D/g, ''));
      });

      // Anexa arquivos locais que ainda não foram enviados
      uploaderState.localFiles.forEach(f => {
        formData.append('images[]', f);
      });

      try {
        const route = propertyId ? 'properties/update' : 'properties/store';
        await API.upload(route, formData);
        toast('Imóvel salvo com sucesso!', 'success');
        setTimeout(() => location.href = API.BASE_URL + '/frontend/pages/admin/imoveis.php', 900);
      } catch (err) {
        toast(err.message || 'Erro ao salvar.', 'error');
        btn.disabled = false;
        btn.innerHTML = original;
        uploaderState.isSubmitting = false;
      }
    });
  }

  // ============================================================
  // CARREGAR IMÓVEL PARA EDIÇÃO
  // ============================================================
  async function loadPropertyForEdit(id) {
    try {
      const prop = await API.properties.get(id);
      if (!prop) return;

      const fields = [
        'code','title','description','purpose','status',
        'price','condo_fee','iptu','area_total','area_built',
        'bedrooms','suites','bathrooms','parking_spaces','floor',
        'zipcode','street','number','complement','neighborhood',
        'city','state','latitude','longitude',
        'category_id','property_type_id'
      ];

      fields.forEach(f => {
        const input = document.querySelector(`[name="${f}"]`);
        if (!input || prop[f] === null || prop[f] === undefined) return;
        if (['price','condo_fee','iptu'].includes(f)) {
          input.value = Number(prop[f]).toLocaleString('pt-BR');
        } else {
          input.value = prop[f];
        }
      });

      const featured = document.querySelector('[name="featured"]');
      if (featured) featured.checked = !!prop.featured;
      const published = document.querySelector('[name="published"]');
      if (published) published.checked = !!prop.published;

      // Features
      if (prop.features) {
        const ids = prop.features.map(f => String(f.id));
        document.querySelectorAll('[name="features[]"]').forEach(cb => {
          cb.checked = ids.includes(cb.value);
          if (cb.checked) cb.closest('.filter-chip')?.classList.add('is-active');
        });
        updateFeaturesCounter();
      }

      // Imagens existentes
      if (prop.images && prop.images.length) renderExistingImages(prop.images);
    } catch (err) {
      console.warn('[loadPropertyForEdit]', err);
    }
  }

  // ============================================================
  // CATEGORIAS E FEATURES
  // ============================================================
  async function loadCategoriesAndFeatures() {
    try {
      const cats = await API.categories();
      const selCat = document.getElementById('propCategory');
      if (selCat && Array.isArray(cats)) {
        const current = selCat.value;
        selCat.innerHTML = '<option value="">Selecione</option>' +
          cats.map(c => `<option value="${c.id}">${esc(c.name)}</option>`).join('');
        if (current) selCat.value = current;
      }
    } catch (e) { console.warn(e); }

    try {
      const types = await API.propertyTypes();
      const selType = document.getElementById('propType');
      if (selType && Array.isArray(types)) {
        const current = selType.value;
        selType.innerHTML = '<option value="">Selecione</option>' +
          types.map(t => `<option value="${t.id}">${esc(t.name)}</option>`).join('');
        if (current) selType.value = current;
      }
    } catch (e) { console.warn(e); }

    try {
      const feats = await API.features();
      const wrap = document.getElementById('featuresList');
      if (wrap && Array.isArray(feats)) {
        wrap.innerHTML = feats.map(f => `
          <label class="filter-chip">
            <input type="checkbox" name="features[]" value="${f.id}">
            ${esc(f.name)}
          </label>
        `).join('');
        wrap.querySelectorAll('.filter-chip input').forEach(input => {
          input.addEventListener('change', () => {
            input.closest('.filter-chip').classList.toggle('is-active', input.checked);
            updateFeaturesCounter();
          });
        });
        updateFeaturesCounter();
      }
    } catch (e) { console.warn(e); }
  }

  function updateFeaturesCounter() {
    const counter = document.getElementById('featuresCounter');
    if (!counter) return;
    const n = document.querySelectorAll('[name="features[]"]:checked').length;
    counter.textContent = n === 1 ? '1 selecionada' : `${n} selecionadas`;
  }

  // ============================================================
  // UPLOAD DE FOTOS
  // ============================================================
  function setupImageUploader(propertyId, state) {
    console.log('[upload] setupImageUploader iniciado, propertyId:', propertyId);

    const input = document.getElementById('imageInput');
    const wrap  = document.getElementById('imageUploader');

    if (!input || !wrap) {
      console.warn('[upload] input ou wrap não encontrado');
      return;
    }

    // Remove listeners antigos clonando (evita duplicação em SPA)
    const newInput = input.cloneNode(true);
    input.parentNode.replaceChild(newInput, input);

    // Handler de mudança
    newInput.addEventListener('change', () => {
      const files = Array.from(newInput.files || []);
      newInput.value = '';
      if (!files.length) return;
      handleFiles(files, propertyId, wrap, state);
    });

    // Delegação de eventos para botões (excluir / principal)
    wrap.addEventListener('click', async (e) => {
      const btn = e.target.closest('.image-uploader__action');
      if (!btn) return;

      e.preventDefault();
      e.stopPropagation();

      const item = btn.closest('.image-uploader__item');
      if (!item) return;

      const action = btn.dataset.action;

      // ---- REMOVER ----
      if (action === 'remove') {
        const imageId = item.dataset.imageId;
        const isLocal = item.dataset.local === '1';

        // Foto local (ainda não enviada): só remove do estado
        if (isLocal) {
          const idx = state.localFiles.findIndex(f => f.__previewId === item.dataset.previewId);
          if (idx >= 0) state.localFiles.splice(idx, 1);
          revokePreview(item);
          item.remove();
          updateImageCounter(wrap);
          return;
        }

        // Foto já no servidor
        if (!confirmDialog('Tem certeza que deseja excluir esta foto?')) return;

        try {
          await API.properties.removeImage(Number(imageId));
          item.remove();
          updateImageCounter(wrap);
          toast('Foto excluída.', 'success');
        } catch (err) {
          toast(err.message || 'Erro ao excluir a foto.', 'error');
        }
        return;
      }

      // ---- DEFINIR PRINCIPAL ----
      if (action === 'primary') {
        wrap.querySelectorAll('.image-uploader__item').forEach(el => el.classList.remove('is-primary'));
        item.classList.add('is-primary');

        const imageId = item.dataset.imageId;
        // Foto local: só marca visualmente, definimos no próximo save
        if (!imageId) return;

        try {
          await API.properties.setPrimaryImage(propertyId, Number(imageId));
          toast('Foto principal definida.', 'success');
        } catch (err) {
          toast(err.message || 'Erro ao definir a foto principal.', 'error');
        }
      }
    });

    console.log('[upload] sistema pronto');
  }

  // ------------------------------------------------------------
  async function handleFiles(files, propertyId, wrap, state) {
    const drop = wrap.querySelector('.image-uploader__drop');

    // ---- SEM propertyId: só preview local ----
    if (!propertyId) {
      files.forEach(file => {
        const url = URL.createObjectURL(file);
        const previewId = 'p_' + Math.random().toString(36).slice(2);
        file.__previewId = previewId;

        const item = createPreviewItem(url, null, false, true);
        item.dataset.previewId = previewId;
        wrap.insertBefore(item, drop);

        state.localFiles.push(file);
      });

      updateImageCounter(wrap);
      toast(files.length + ' foto(s) pronta(s). Serão enviadas ao salvar.', 'info');
      return;
    }

    // ---- COM propertyId: upload imediato ----
    const formData = new FormData();
    formData.append('property_id', propertyId);
    files.forEach(f => formData.append('images[]', f));

    // Previews otimistas
    const temps = files.map(file => {
      const url = URL.createObjectURL(file);
      const item = createPreviewItem(url, null, false, true);
      wrap.insertBefore(item, drop);
      return { item, url };
    });

    updateImageCounter(wrap);

    try {
      const res = await API.properties.uploadImages(formData);
      const uploaded = res.images || [];

      // Remove previews temporários
      temps.forEach(({ item, url }) => {
        URL.revokeObjectURL(url);
        item.remove();
      });

      // Insere as imagens reais
      const hasPrimary = wrap.querySelector('.image-uploader__item.is-primary');
      uploaded.forEach((img, i) => {
        const url = UPLOAD_URL + img.filename;
        const isFirst = !hasPrimary && i === 0;
        const item = createPreviewItem(url, img.id, isFirst, false);
        wrap.insertBefore(item, drop);
      });

      updateImageCounter(wrap);
      toast(uploaded.length + ' foto(s) adicionada(s).', 'success');
    } catch (err) {
      temps.forEach(({ item, url }) => {
        URL.revokeObjectURL(url);
        item.remove();
      });
      updateImageCounter(wrap);
      toast('Erro ao enviar fotos: ' + (err.message || 'erro desconhecido'), 'error');
      console.error(err);
    }
  }

  // ------------------------------------------------------------
  function createPreviewItem(url, imageId, isPrimary, isLocal) {
    const item = document.createElement('div');
    item.className = 'image-uploader__item' + (isPrimary ? ' is-primary' : '');
    if (imageId) item.dataset.imageId = imageId;
    if (isLocal) item.dataset.local = '1';

    item.innerHTML = `
      <img src="${url}" alt="" loading="lazy">
      <div class="image-uploader__actions">
        <button type="button" class="image-uploader__action" data-action="primary" title="Foto principal">⭐</button>
        <button type="button" class="image-uploader__action" data-action="remove" title="Excluir foto">✕</button>
      </div>
    `;

    return item;
  }

  // ------------------------------------------------------------
  function renderExistingImages(images) {
    const wrap = document.getElementById('imageUploader');
    const drop = wrap?.querySelector('.image-uploader__drop');
    if (!wrap || !drop) return;

    // Limpa itens existentes e previews pendentes
    wrap.querySelectorAll('.image-uploader__item').forEach(el => {
      revokePreview(el);
      el.remove();
    });

    // Ordena: principal primeiro
    const sorted = [...images].sort((a, b) => (b.is_primary == 1) - (a.is_primary == 1));

    sorted.forEach(img => {
      const url = UPLOAD_URL + img.filename;
      const item = createPreviewItem(url, img.id, img.is_primary == 1, false);
      wrap.insertBefore(item, drop);
    });

    updateImageCounter(wrap);
  }

  // ------------------------------------------------------------
  function updateImageCounter(wrap) {
    const counter = document.getElementById('imageCounter');
    if (!wrap || !counter) return;
    const count = wrap.querySelectorAll('.image-uploader__item').length;
    counter.textContent = count === 1 ? '1 foto' : count + ' fotos';
  }

  // ------------------------------------------------------------
  function revokePreview(item) {
    const img = item.querySelector('img');
    if (img && img.src.startsWith('blob:')) {
      try { URL.revokeObjectURL(img.src); } catch (_) {}
    }
  }

  // ============================================================
  // GESTÃO DE IMÓVEIS
  // ============================================================
  function initProperties() {
    const tbody = document.getElementById('propertiesTable');
    if (!tbody) return;

    const filters = { q: '', status: '', purpose: '', page: 1 };
    const search = document.getElementById('searchProperties');
    const status = document.getElementById('filterStatus');
    const purpose = document.getElementById('filterPurpose');
    const pagination = document.getElementById('propertiesPagination');

    const load = async (page = filters.page) => {
      filters.page = page;
      tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:32px;">Carregando…</td></tr>';
      try {
        const result = await API.properties.adminList(filters);
        const items = result.items || [];
        if (!items.length) {
          tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:32px;">Nenhum imóvel encontrado.</td></tr>';
          pagination.innerHTML = '';
          return;
        }

        const statusLabels = {
          disponivel: 'Disponível',
          reservado: 'Reservado',
          vendido: 'Vendido',
          alugado: 'Alugado',
          inativo: 'Inativo',
        };

        tbody.innerHTML = items.map(property => `
          <tr>
            <td><img src="${esc(imageUrl(property.main_image))}" class="admin-table__thumb" alt="" loading="lazy"></td>
            <td><span class="admin-table__title">${esc(property.title)}</span><span class="admin-table__meta">#${esc(property.code)}</span></td>
            <td>${esc([property.neighborhood, property.city].filter(Boolean).join(' — ') || '—')}</td>
            <td>${esc(Number(property.price || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }))}</td>
            <td>${esc(statusLabels[property.status] || property.status)}</td>
            <td>
              <div class="admin-table__actions">
                <button type="button" class="admin-action-btn" data-property-action="published" data-property-id="${Number(property.id)}" aria-label="${property.published ? 'Despublicar' : 'Publicar'} imóvel">
                  ${property.published ? 'Publicado' : 'Rascunho'}
                </button>
                <button type="button" class="admin-action-btn" data-property-action="featured" data-property-id="${Number(property.id)}" aria-label="${property.featured ? 'Remover destaque' : 'Destacar'} imóvel">
                  ${property.featured ? '★ Destaque' : '☆ Destacar'}
                </button>
              </div>
            </td>
            <td>
              <div class="admin-table__actions">
                <a href="${API.BASE_URL}/frontend/pages/admin/imovel-form.php?id=${Number(property.id)}" class="admin-action-btn" aria-label="Editar imóvel">Editar</a>
                <button type="button" class="admin-action-btn" data-property-action="duplicate" data-property-id="${Number(property.id)}">Duplicar</button>
                <button type="button" class="admin-action-btn" data-property-action="delete" data-property-id="${Number(property.id)}">Excluir</button>
              </div>
            </td>
          </tr>
        `).join('');

        const pages = Number(result.pages || 1);
        pagination.innerHTML = pages > 1 ? `
          <div class="admin-toolbar" style="justify-content:space-between;">
            <span>Página ${filters.page} de ${pages} (${Number(result.total || 0)} imóveis)</span>
            <div class="admin-table__actions">
              <button type="button" class="btn btn-ghost btn-sm" data-properties-page="${filters.page - 1}" ${filters.page <= 1 ? 'disabled' : ''}>Anterior</button>
              <button type="button" class="btn btn-ghost btn-sm" data-properties-page="${filters.page + 1}" ${filters.page >= pages ? 'disabled' : ''}>Próxima</button>
            </div>
          </div>
        ` : '';
      } catch (err) {
        console.error('[admin properties]', err);
        tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:#b91c1c;padding:32px;">Erro ao carregar imóveis. Atualize a página ou tente novamente.</td></tr>';
      }
    };

    search?.addEventListener('input', debounce(event => {
      filters.q = event.target.value.trim();
      load(1);
    }, 350));
    status?.addEventListener('change', event => {
      filters.status = event.target.value;
      load(1);
    });
    purpose?.addEventListener('change', event => {
      filters.purpose = event.target.value;
      load(1);
    });

    tbody.addEventListener('click', async event => {
      const button = event.target.closest('[data-property-action]');
      if (!button) return;

      const id = Number(button.dataset.propertyId);
      const action = button.dataset.propertyAction;
      try {
        if (action === 'delete') {
          if (!confirmDialog('Excluir este imóvel? Ele será ocultado do site e mantido no banco de dados.')) return;
          await API.properties.remove(id);
          toast('Imóvel movido para a lixeira por 30 dias.', 'success');
        } else if (action === 'duplicate') {
          if (!confirmDialog('Criar uma cópia deste imóvel como rascunho?')) return;
          await API.properties.duplicate(id);
          toast('Imóvel duplicado como rascunho.', 'success');
        } else if (action === 'featured') {
          await API.properties.toggleFeatured(id);
          toast('Destaque atualizado.', 'success');
        } else if (action === 'published') {
          await API.properties.togglePublished(id);
          toast('Publicação atualizada.', 'success');
        }
        await load();
      } catch (err) {
        console.error('[admin property action]', err);
        toast(err.message || 'Não foi possível atualizar o imóvel.', 'error');
      }
    });

    pagination?.addEventListener('click', event => {
      const button = event.target.closest('[data-properties-page]');
      if (button && !button.disabled) load(Number(button.dataset.propertiesPage));
    });

    load(1);
  }

  // ============================================================
  // LEADS
  // ============================================================
  function initLeads() {
    const tbody = document.getElementById('leadsTable');
    if (!tbody) return;

    const filters = { q: '', status: '' };

    document.getElementById('searchLeads')?.addEventListener('input', debounce((e) => {
      filters.q = e.target.value.trim();
      load();
    }, 400));

    document.getElementById('filterLeadStatus')?.addEventListener('change', (e) => {
      filters.status = e.target.value;
      load();
    });

    async function load() {
      tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:32px;">Carregando…</td></tr>';
      try {
        const data = await API.leads.list(filters);
        const items = data.items || data || [];
        if (!items.length) {
          tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:32px;">Nenhum lead.</td></tr>';
          return;
        }
        tbody.innerHTML = items.map(l => `
          <tr>
            <td>${esc(l.name)}</td>
            <td>${esc(l.email)}</td>
            <td>${esc(l.phone || '')}</td>
            <td>${l.property_title ? esc(l.property_title) : '—'}</td>
            <td>${formatDate(l.created_at)}</td>
            <td>—</td>
          </tr>
        `).join('');
      } catch (err) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:red;">Erro ao carregar.</td></tr>';
      }
    }

    load();
  }

  function formatDate(dt) {
    if (!dt) return '—';
    return new Date(dt).toLocaleDateString('pt-BR');
  }

  // ============================================================
  // ROUTER
  // ============================================================
  const modules = {
    'dashboard':    initDashboard,
    'imovel-form':  initImovelForm,
    'imoveis':      initProperties,
    'leads':        initLeads,
  };

  if (modules[section]) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', modules[section]);
    } else {
      modules[section]();
    }
  }
})();