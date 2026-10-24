'use strict';

(() => {
  const page = document.getElementById('propertyPage');
  if (!page) return;

  const propertyId = Number.parseInt(page.dataset.propertyId || '0', 10);
  let currentProperty = null;
  let galleryImages = [];
  let galleryIndex = 0;

  if (!propertyId) {
    showError();
    return;
  }

  async function load() {
    try {
      const property = await API.properties.get(propertyId);
      if (!property || !property.id) throw new Error('Imóvel não encontrado');

      currentProperty = property;
      render(property);
      bindActions();
      loadFavoriteState();
      loadSimilarProperties();
    } catch (error) {
      console.error('[property-details]', error);
      showError();
    }
  }

  function showError() {
    const loading = document.getElementById('propertyLoading');
    const error = document.getElementById('propertyError');
    const content = document.getElementById('propertyContent');
    if (loading) loading.hidden = true;
    if (error) error.hidden = false;
    if (content) content.hidden = true;
  }

  function render(property) {
    const loading = document.getElementById('propertyLoading');
    const content = document.getElementById('propertyContent');
    if (loading) loading.hidden = true;
    if (content) content.hidden = false;

    setText('breadcrumbTitle', property.title);
    setText('propertyTitle', property.title);
    setText('propertyCode', property.code ? `#${property.code}` : `#${property.id}`);
    setText('propertyLocation', [property.neighborhood, property.city, property.state].filter(Boolean).join(' — ') || 'Localização não informada');
    setText('propertyDescription', property.description || 'Sem descrição.');
    setText('priceLabel', property.purpose === 'aluguel' ? 'Aluguel mensal' : 'Valor de venda');
    setText('propertyPrice', Number(property.price) > 0 ? formatMoney(property.price) : 'Sob consulta');
    setText('propertyCondo', Number(property.condo_fee) > 0 ? formatMoney(property.condo_fee) : 'Isento');
    setText('propertyIptu', Number(property.iptu) > 0 ? formatMoney(property.iptu) : 'Isento');

    renderBadges(property);
    renderMainFeatures(property);
    renderFeatureList(property.features || []);
    renderAgent(property.agent);
    renderMap(property);
    setupGallery(property.images || []);
  }

  function renderBadges(property) {
    const badges = document.getElementById('propertyBadges');
    if (!badges) return;
    badges.replaceChildren();

    const labels = [
      property.purpose === 'aluguel' ? 'Aluguel' : 'Venda',
      property.property_type_name,
      property.featured ? 'Destaque' : '',
    ].filter(Boolean);

    labels.forEach(label => {
      const badge = document.createElement('span');
      badge.className = 'property-card__badge';
      badge.textContent = label;
      badges.append(badge);
    });
  }

  function renderMainFeatures(property) {
    const container = document.getElementById('propertyFeatures');
    if (!container) return;

    const details = [
      ['Quartos', property.bedrooms],
      ['Suítes', property.suites],
      ['Banheiros', property.bathrooms],
      ['Vagas', property.parking_spaces],
      ['Área construída', property.area_built ? `${property.area_built} m²` : ''],
    ].filter(([, value]) => Number(value) > 0 || (typeof value === 'string' && value));

    container.replaceChildren(...details.map(([label, value]) => {
      const item = document.createElement('div');
      item.className = 'property-feature-main';

      const number = document.createElement('span');
      number.className = 'property-feature-main__value';
      number.textContent = String(value);

      const caption = document.createElement('span');
      caption.className = 'property-feature-main__label';
      caption.textContent = label;

      item.append(number, caption);
      return item;
    }));
  }

  function renderFeatureList(features) {
    const wrapper = document.getElementById('propertyFeatureListWrapper');
    const list = document.getElementById('propertyFeatureList');
    if (!wrapper || !list) return;

    list.replaceChildren(...features.map(feature => {
      const item = document.createElement('div');
      item.className = 'property-feature-item';
      item.textContent = feature.name || '';
      return item;
    }));
    wrapper.hidden = features.length === 0;
  }

  function renderAgent(agent) {
    const name = agent?.name || 'Equipe DIOMAR_CS';
    const phone = normalizePhone(agent?.phone || agent?.whatsapp);
    const whatsapp = normalizePhone(agent?.whatsapp || agent?.phone);

    setText('agentName', name);
    setText('agentCreci', agent?.creci ? `CRECI ${agent.creci}` : '');
    setText('agentAvatar', name.split(/\s+/).slice(0, 2).map(part => part[0]).join('').toUpperCase());

    setContactLinks(phone, whatsapp);
    if (!phone || !whatsapp) {
      API.settings.get().then(settings => {
        setContactLinks(
          phone || normalizePhone(settings.site_phone || settings.site_whatsapp),
          whatsapp || normalizePhone(settings.site_whatsapp || settings.site_phone)
        );
      }).catch(error => console.error('[property-contact-settings]', error));
    }
  }

  function setContactLinks(phone, whatsapp) {
    const whatsappButton = document.getElementById('btnWhatsapp');
    if (whatsappButton) {
      if (whatsapp) {
        whatsappButton.hidden = false;
        whatsappButton.href = `https://wa.me/${whatsapp}?text=${encodeURIComponent(`Olá! Tenho interesse no imóvel ${currentProperty?.code || ''} - ${currentProperty?.title || ''}.`)}`;
        whatsappButton.target = '_blank';
        whatsappButton.rel = 'noopener noreferrer';
      } else {
        whatsappButton.hidden = true;
      }
    }

    const phoneButton = document.getElementById('btnPhone');
    if (phoneButton) {
      if (phone) {
        phoneButton.hidden = false;
        phoneButton.href = `tel:+${phone}`;
      } else {
        phoneButton.hidden = true;
      }
    }
  }

  function renderMap(property) {
    const map = document.getElementById('propertyMap');
    if (!map) return;

    const hasCoordinates = property.latitude !== null && property.latitude !== '' &&
      property.longitude !== null && property.longitude !== '' &&
      Number.isFinite(Number(property.latitude)) && Number.isFinite(Number(property.longitude));
    const address = [
      property.street,
      property.number,
      property.neighborhood,
      property.city,
      property.state,
      property.zipcode,
    ].filter(Boolean).join(', ');
    const query = hasCoordinates
      ? `${property.latitude},${property.longitude}`
      : address;

    if (!query) {
      map.textContent = 'Localização não informada para este imóvel.';
      return;
    }

    const frame = document.createElement('iframe');
    frame.src = `https://www.google.com/maps?q=${encodeURIComponent(query)}&output=embed`;
    frame.title = `Mapa: ${address || query}`;
    frame.loading = 'lazy';
    frame.referrerPolicy = 'no-referrer-when-downgrade';
    frame.allowFullscreen = true;
    map.replaceChildren(frame);
  }

  function setupGallery(images) {
    galleryImages = images.length ? images : [{ filename: currentProperty?.main_image || null }];
    galleryIndex = 0;
    renderGalleryThumbs();
    updateGallery();

    document.getElementById('galleryPrev')?.addEventListener('click', () => moveGallery(-1));
    document.getElementById('galleryNext')?.addEventListener('click', () => moveGallery(1));
    document.getElementById('galleryExpand')?.addEventListener('click', () => {
      document.getElementById('galleryThumbs')?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
      document.querySelector('#galleryThumbs .property-gallery__thumb')?.focus();
    });
  }

  function moveGallery(direction) {
    galleryIndex = (galleryIndex + direction + galleryImages.length) % galleryImages.length;
    updateGallery();
  }

  function renderGalleryThumbs() {
    const thumbs = document.getElementById('galleryThumbs');
    if (!thumbs) return;

    thumbs.replaceChildren(...galleryImages.map((image, index) => {
      const button = document.createElement('button');
      button.type = 'button';
      button.className = 'property-gallery__thumb';
      button.setAttribute('aria-label', `Ver imagem ${index + 1}`);
      button.setAttribute('aria-pressed', index === galleryIndex ? 'true' : 'false');

      const thumbnail = document.createElement('img');
      thumbnail.src = getImageUrl(image.filename);
      thumbnail.alt = image.alt_text || '';
      thumbnail.loading = 'lazy';
      button.append(thumbnail);
      button.addEventListener('click', () => {
        galleryIndex = index;
        updateGallery();
      });
      return button;
    }));
  }

  function updateGallery() {
    const image = galleryImages[galleryIndex];
    const main = document.getElementById('galleryMain');
    const counter = document.getElementById('galleryCounter');

    if (main) {
      main.src = getImageUrl(image?.filename);
      main.alt = image?.alt_text || currentProperty?.title || 'Imagem do imóvel';
      main.onerror = () => {
        main.onerror = null;
        main.src = getImageUrl(null);
      };
    }
    if (counter) counter.textContent = `${galleryIndex + 1} / ${galleryImages.length}`;

    document.querySelectorAll('#galleryThumbs .property-gallery__thumb').forEach((thumb, index) => {
      const active = index === galleryIndex;
      thumb.classList.toggle('is-active', active);
      thumb.setAttribute('aria-pressed', active ? 'true' : 'false');
    });
  }

  function getImageUrl(filename) {
    const baseUrl = API.BASE_URL || '';
    return filename && filename !== 'placeholder.svg'
      ? `${baseUrl}/backend/uploads/properties/${encodeURIComponent(filename)}`
      : `${baseUrl}/frontend/images/placeholder.jpg`;
  }

  function bindActions() {
    document.getElementById('interestForm')?.addEventListener('submit', submitInterest);
    document.getElementById('btnFavorite')?.addEventListener('click', toggleFavorite);
    document.getElementById('btnShare')?.addEventListener('click', shareProperty);

    document.querySelectorAll('[data-share]').forEach(button => {
      button.addEventListener('click', event => {
        event.preventDefault();
        shareTo(button.dataset.share);
      });
    });
  }

  async function submitInterest(event) {
    event.preventDefault();
    const form = event.currentTarget;
    const submitButton = form.querySelector('[type="submit"]');
    const originalText = submitButton.textContent;
    const payload = Object.fromEntries(new FormData(form).entries());
    payload.property_id = propertyId;

    submitButton.disabled = true;
    submitButton.textContent = 'Enviando...';
    try {
      await API.leads.create(payload);
      form.reset();
      App.toast('Interesse enviado. Em breve entraremos em contato.', 'success');
    } catch (error) {
      console.error('[property-lead]', error);
      App.toast(error.status === 404
        ? 'Este imóvel não está mais disponível.'
        : (error.message || 'Não foi possível enviar seu interesse.'), 'error');
    } finally {
      submitButton.disabled = false;
      submitButton.textContent = originalText;
    }
  }

  async function loadFavoriteState() {
    try {
      const favorites = await API.favorites.list();
      const isFavorite = Array.isArray(favorites) && favorites.some(item =>
        Number(item.property_id || item.id) === propertyId
      );
      updateFavoriteButton(isFavorite);
    } catch (error) {
      if (error.status !== 401) console.error('[property-favorite-state]', error);
    }
  }

  async function toggleFavorite() {
    const button = document.getElementById('btnFavorite');
    if (!button) return;
    button.disabled = true;
    try {
      const result = await API.favorites.toggle(propertyId);
      updateFavoriteButton(result.favorited);
      App.toast(result.favorited ? 'Adicionado aos favoritos' : 'Removido dos favoritos', 'success');
      document.dispatchEvent(new CustomEvent('favorites:changed', { detail: { count: result.total } }));
    } catch (error) {
      if (error.status === 401) {
        App.toast('Faça login para salvar este imóvel.', 'warning');
        setTimeout(() => { window.location.href = `${API.BASE_URL}/frontend/pages/login.php`; }, 900);
      } else {
        console.error('[property-favorite]', error);
        App.toast(error.message || 'Não foi possível atualizar os favoritos.', 'error');
      }
    } finally {
      button.disabled = false;
    }
  }

  function updateFavoriteButton(favorited) {
    const button = document.getElementById('btnFavorite');
    if (!button) return;
    button.classList.toggle('is-active', favorited);
    button.setAttribute('aria-pressed', favorited ? 'true' : 'false');
    const label = button.lastChild;
    if (label?.nodeType === Node.TEXT_NODE) label.textContent = favorited ? ' Favoritado' : ' Favoritar';
  }

  async function loadSimilarProperties() {
    try {
      const result = await API.properties.similar(propertyId);
      const properties = Array.isArray(result) ? result : result?.items;
      const section = document.getElementById('similarSection');
      const grid = document.getElementById('similarGrid');
      if (!section || !grid || !Array.isArray(properties) || !properties.length) return;

      grid.innerHTML = properties.map(property => {
        const image = property.main_image
          ? `${API.BASE_URL}/backend/uploads/properties/${encodeURIComponent(property.main_image)}`
          : `${API.BASE_URL}/frontend/images/placeholder.jpg`;
        const location = [property.neighborhood, property.city].filter(Boolean).join(' — ');
        return `<article class="property-card">
          <a class="property-card__link" href="${API.BASE_URL}/frontend/pages/imovel.php?id=${Number(property.id)}">
            <div class="property-card__image"><img src="${image}" alt="${escapeHtml(property.title)}" loading="lazy"></div>
            <div class="property-card__body">
              <h3 class="property-card__title">${escapeHtml(property.title)}</h3>
              <p class="property-card__location">${escapeHtml(location)}</p>
              <span class="property-card__price-value">${Number(property.price) > 0 ? formatMoney(property.price) : 'Sob consulta'}</span>
            </div>
          </a>
        </article>`;
      }).join('');
      section.hidden = false;
    } catch (error) {
      console.error('[similar-properties]', error);
    }
  }

  async function shareProperty() {
    const url = window.location.href;
    const title = currentProperty?.title || 'Imóvel';
    if (navigator.share) {
      try {
        await navigator.share({ title, text: `Confira este imóvel: ${title}`, url });
      } catch (error) {
        if (error.name !== 'AbortError') {
          console.error('[property-share]', error);
          App.toast('Não foi possível compartilhar este imóvel.', 'error');
        }
      }
      return;
    }
    await copyLink(url);
  }

  function shareTo(channel) {
    const url = window.location.href;
    const title = currentProperty?.title || 'Imóvel';
    const text = `Confira este imóvel: ${title} ${url}`;
    const targets = {
      whatsapp: `https://wa.me/?text=${encodeURIComponent(text)}`,
      facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`,
      twitter: `https://twitter.com/intent/tweet?text=${encodeURIComponent(`Confira este imóvel: ${title}`)}&url=${encodeURIComponent(url)}`,
    };

    if (channel === 'copy') {
      copyLink(url);
    } else if (targets[channel]) {
      window.open(targets[channel], '_blank', 'noopener,noreferrer');
    }
  }

  async function copyLink(url) {
    try {
      if (navigator.clipboard?.writeText) {
        await navigator.clipboard.writeText(url);
      } else {
        const input = document.createElement('textarea');
        input.value = url;
        input.style.position = 'fixed';
        input.style.opacity = '0';
        document.body.append(input);
        input.select();
        const copied = document.execCommand('copy');
        input.remove();
        if (!copied) throw new Error('Não foi possível copiar o link.');
      }
      App.toast('Link copiado para a área de transferência.', 'success');
    } catch (error) {
      console.error('[property-copy-link]', error);
      App.toast('Não foi possível copiar o link.', 'error');
    }
  }

  function normalizePhone(phone) {
    let digits = String(phone || '').replace(/\D/g, '');
    if (digits.length === 10 || digits.length === 11) digits = `55${digits}`;
    return digits;
  }

  function formatMoney(value) {
    return `R$ ${Number(value).toLocaleString('pt-BR', { minimumFractionDigits: 0, maximumFractionDigits: 2 })}`;
  }

  function escapeHtml(value) {
    const element = document.createElement('div');
    element.textContent = String(value ?? '');
    return element.innerHTML;
  }

  function setText(id, text) {
    const element = document.getElementById(id);
    if (element) element.textContent = text || '';
  }

  load();
})();
