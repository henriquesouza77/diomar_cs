'use strict';

(() => {
  const form = document.getElementById('heroSearch');
  if (!form) return;

  // CORREÇÃO: A classe no HTML é .hero__tab
  const tabs = form.querySelectorAll('.hero__tab');
  const purposeInput = document.getElementById('heroPurpose');
  const slider = form.querySelector('.hero__tab-slider');

  // ============ TABS COM SLIDER ============
  function setActiveTab(activeTab) {
    tabs.forEach(t => {
      const isActive = t === activeTab;
      t.classList.toggle('is-active', isActive);
      t.setAttribute('aria-selected', isActive ? 'true' : 'false');
    });

    const purpose = activeTab.dataset.purpose;
    if (purposeInput) purposeInput.value = purpose;

    // Move o slider visualmente
    if (slider) {
      const index = Array.from(tabs).indexOf(activeTab);
      slider.style.transform = `translateX(${index * 100}%)`;
    }
  }

  tabs.forEach(tab => {
    tab.addEventListener('click', () => setActiveTab(tab));
  });

  // ============ MÁSCARA DE PREÇO ============
  const priceInput = document.getElementById('heroMaxPrice');
  if (priceInput) {
    priceInput.addEventListener('input', (e) => {
      let value = e.target.value.replace(/\D/g, '');
      if (value) {
        value = Number(value).toLocaleString('pt-BR');
      }
      e.target.value = value;
    });
  }

  // ============ SUBMIT ============
  form.addEventListener('submit', (e) => {
    if (priceInput && priceInput.value) {
      priceInput.value = priceInput.value.replace(/\D/g, '');
    }

    const purpose = purposeInput?.value;
    if (!purpose) {
      e.preventDefault();
      App.toast('Selecione Comprar ou Alugar.', 'warning');
      return;
    }

    sessionStorage.setItem('lastSearch', JSON.stringify({
      purpose,
      type: form.querySelector('#heroType')?.value || '',
      bedrooms: form.querySelector('#heroBedrooms')?.value || '',
      max_price: priceInput?.value || '',
    }));
  });

  // ============ RESTAURA ÚLTIMA BUSCA ============
  const saved = sessionStorage.getItem('lastSearch');
  if (saved) {
    try {
      const data = JSON.parse(saved);
      if (data.purpose) {
        const tab = [...tabs].find(t => t.dataset.purpose === data.purpose);
        if (tab) setActiveTab(tab);
      }
      if (data.type)    form.querySelector('#heroType').value = data.type;
      if (data.bedrooms) form.querySelector('#heroBedrooms').value = data.bedrooms;
      if (data.max_price && priceInput) {
        priceInput.value = Number(data.max_price).toLocaleString('pt-BR');
      }
    } catch (err) {
      console.warn('[hero-search] Estado inválido');
    }
  }

  // ============ PARALLAX SUTIL DO BACKGROUND ============
  const heroBg = document.querySelector('.hero__bg img');
  if (heroBg && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    let ticking = false;
    window.addEventListener('scroll', () => {
      if (ticking) return;
      requestAnimationFrame(() => {
        const y = window.scrollY;
        if (y < window.innerHeight) {
          heroBg.style.transform = `scale(1.05) translateY(${y * 0.15}px)`;
        }
        ticking = false;
      });
      requestAnimationFrame(() => ticking = false);
    }, { passive: true });
  }
})(); 