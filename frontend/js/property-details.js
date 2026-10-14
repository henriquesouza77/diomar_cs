'use strict';

(() => {
  // CORREÇÃO AQUI: Verifica se a div do imóvel existe em vez da variável
  const $page = document.getElementById('propertyPage');
  if (!$page) return; 

  const propertyId = parseInt($page.dataset.propertyId || '0', 10);
  let currentProperty = null;
  let galleryImages = [];
  let galleryIndex = 0;

  if (!propertyId) {
    showError();
    return;
  }

  async function load() {
    try {
      const data = await API.properties.get(propertyId);
      if (!data || !data.id) throw new Error('Imóvel não encontrado');
      currentProperty = data;
      render(data);
    } catch (err) {
      console.error('[Erro]', err);
      showError();
    }
  }

  function showError() {
    const elLoad = document.getElementById('propertyLoading');
    const elErr = document.getElementById('propertyError');
    const elCont = document.getElementById('propertyContent');
    if (elLoad) elLoad.hidden = true;
    if (elErr) elErr.hidden = false;
    if (elCont) elCont.hidden = true;
  }

  function render(p) {
    const elLoad = document.getElementById('propertyLoading');
    const elErr = document.getElementById('propertyError');
    const elCont = document.getElementById('propertyContent');
    if (elLoad) elLoad.hidden = true;
    if (elErr) elErr.hidden = true;
    if (elCont) elCont.hidden = false;

    setText('breadcrumbTitle', p.title);
    setText('propertyTitle', p.title);
    setText('propertyCode', p.code ? `#${p.code}` : `#${p.id}`);
    setText('propertyLocation', p.city ? `${p.neighborhood || ''} - ${p.city}` : 'Localização não informada');
    setText('propertyDescription', p.description || 'Sem descrição.');

    setText('priceLabel', p.purpose === 'aluguel' ? 'Aluguel mensal' : 'Valor de venda');
    setText('propertyPrice', p.price ? `R$ ${Number(p.price).toLocaleString('pt-BR')}` : 'Sob consulta');
    setText('propertyCondo', p.condo_fee > 0 ? `R$ ${Number(p.condo_fee).toLocaleString('pt-BR')}` : 'Isento');
    setText('propertyIptu', p.iptu > 0 ? `R$ ${Number(p.iptu).toLocaleString('pt-BR')}` : 'Isento');

    setupGallery(p.images || []);
  }

  function setupGallery(images) {
    galleryImages = (images && images.length > 0) ? images : [{ filename: currentProperty?.main_image || null }];
    galleryIndex = 0;
    renderGalleryThumbs();
    updateGallery();

    const btnPrev = document.getElementById('galleryPrev');
    const btnNext = document.getElementById('galleryNext');
    
    if (btnPrev) {
      btnPrev.onclick = () => {
        galleryIndex = (galleryIndex - 1 + galleryImages.length) % galleryImages.length;
        updateGallery();
      };
    }
    if (btnNext) {
      btnNext.onclick = () => {
        galleryIndex = (galleryIndex + 1) % galleryImages.length;
        updateGallery();
      };
    }
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
      thumbnail.alt = '';
      thumbnail.loading = 'lazy';
      button.append(thumbnail);
      button.addEventListener('click', () => {
        galleryIndex = index;
        updateGallery();
      });

      return button;
    }));
  }

  function getImageUrl(filename) {
    const baseUrl = (typeof API !== 'undefined' && API.BASE_URL) ? API.BASE_URL : '/diomar-cs';
    return filename && filename !== 'placeholder.svg'
      ? `${baseUrl}/backend/uploads/properties/${encodeURIComponent(filename)}`
      : `${baseUrl}/frontend/images/placeholder.jpg`;
  }

  function updateGallery() {
    const img = galleryImages[galleryIndex];
    const main = document.getElementById('galleryMain');
    const counter = document.getElementById('galleryCounter');
    
    if (main) {
      main.src = getImageUrl(img?.filename);
      main.onerror = () => { main.src = getImageUrl(null); };
    }

    if (counter) {
      counter.textContent = `${galleryIndex + 1} / ${galleryImages.length}`;
    }

    document.querySelectorAll('#galleryThumbs .property-gallery__thumb').forEach((thumb, index) => {
      const isActive = index === galleryIndex;
      thumb.classList.toggle('is-active', isActive);
      thumb.setAttribute('aria-pressed', isActive ? 'true' : 'false');
    });
  }

  function setText(id, text) {
    const el = document.getElementById(id);
    if (el) el.textContent = text || '';
  }

  load();
})();