<?php
$pageTitle       = 'Encontre seu imóvel';
$pageDescription = 'Curadoria de imóveis de alto padrão em São Paulo. Compra, venda e aluguel com assessoria exclusiva.';
$currentPage     = 'home';
$transparentNav  = true;

require_once __DIR__ . '/../components/header.php';

$base = '/diomar-cs';
?>

<!-- ==================== HERO ==================== -->
<section class="hero" id="hero">

  <!-- Fundo com imagem -->
  <div class="hero__media">
    <img src="<?= $base ?>/frontend/images/hero-bg.svg"
         alt=""
         aria-hidden="true"
         fetchpriority="high"
         onerror="this.src='<?= $base ?>/frontend/images/placeholder.svg'">
  </div>

  <div class="hero__overlay" aria-hidden="true"></div>
  <div class="hero__grid" aria-hidden="true"></div>

  <!-- Blobs decorativos -->
  <div class="hero__blob hero__blob--1" aria-hidden="true"></div>
  <div class="hero__blob hero__blob--2" aria-hidden="true"></div>

  <div class="hero__container">

    <!-- Conteúdo esquerdo -->
    <div class="hero__content">

      <span class="hero__eyebrow">
        <span class="hero__eyebrow-dot" aria-hidden="true"></span>
        Curadoria de alto padrão
      </span>

      <h1 class="hero__title">
        <span class="hero__title-line"><span>Seu próximo</span></span>
        <span class="hero__title-line"><span><em>imóvel exclusivo</em></span></span>
        <span class="hero__title-line"><span>está aqui.</span></span>
      </h1>

      <p class="hero__subtitle">
        Seleção criteriosa de residências, coberturas panorâmicas e oportunidades
        únicas em São Paulo e região. Cada imóvel, uma história.
      </p>

      <div class="hero__actions">
        <a href="<?= $base ?>/frontend/pages/imoveis.php?purpose=venda" class="btn btn-gold btn-lg">
          Explorar portfólio
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="5" y1="12" x2="19" y2="12"/>
            <polyline points="12 5 19 12 12 19"/>
          </svg>
        </a>
        <a href="<?= $base ?>/frontend/pages/contato.php" class="btn btn-outline btn-lg" style="color: #fff; border-color: rgba(255,255,255,0.3);">
          Falar com consultor
        </a>
      </div>

      <div class="hero__trust">
        <div class="hero__trust-item">
          <span class="hero__trust-icon" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
          </span>
          Assessoria jurídica completa
        </div>
        <div class="hero__trust-item">
          <span class="hero__trust-icon" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
              <circle cx="9" cy="7" r="4"/>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
          </span>
          Consultor dedicado
        </div>
        <div class="hero__trust-item">
          <span class="hero__trust-icon" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
            </svg>
          </span>
          15 anos de mercado
        </div>
      </div>
    </div>

    <!-- Card de busca -->
    <form class="hero__search" id="heroSearch" action="<?= $base ?>/frontend/pages/imoveis.php" method="get">

      <div class="hero__search-header">
        <h2 class="hero__search-title">Busca inteligente</h2>
        <p class="hero__search-subtitle">Encontre seu imóvel em poucos cliques</p>
      </div>

      <!-- Tabs -->
      <div class="hero__tabs" id="heroTabs" data-active="venda">
        <button type="button" class="hero__tab is-active" data-purpose="venda">Comprar</button>
        <button type="button" class="hero__tab" data-purpose="aluguel">Alugar</button>
        <span class="hero__tab-slider" aria-hidden="true"></span>
      </div>
      <input type="hidden" name="purpose" id="heroPurpose" value="venda">

      <div class="hero__form">

        <div class="hero__field">
          <label class="hero__field-label" for="heroKeyword">Localização</label>
          <div class="hero__field-inner">
            <svg class="hero__field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
              <circle cx="12" cy="10" r="3"/>
            </svg>
            <input type="text" id="heroKeyword" name="q" class="hero__field-input" placeholder="Bairro, cidade ou código">
          </div>
        </div>

        <div class="hero__form-row">
          <div class="hero__field">
            <label class="hero__field-label" for="heroType">Tipo</label>
            <div class="hero__field-inner">
              <svg class="hero__field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
              </svg>
              <select id="heroType" name="type" class="hero__field-select">
                <option value="">Todos</option>
                <option value="apartamento">Apartamento</option>
                <option value="casa">Casa</option>
                <option value="cobertura">Cobertura</option>
                <option value="terreno">Terreno</option>
              </select>
            </div>
          </div>

          <div class="hero__field">
            <label class="hero__field-label" for="heroBedrooms">Quartos</label>
            <div class="hero__field-inner">
              <svg class="hero__field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M2 4v16"/>
                <path d="M2 8h18a2 2 0 0 1 2 2v10"/>
                <path d="M2 17h20"/>
              </svg>
              <select id="heroBedrooms" name="bedrooms" class="hero__field-select">
                <option value="">Qualquer</option>
                <option value="1">1+</option>
                <option value="2">2+</option>
                <option value="3">3+</option>
                <option value="4">4+</option>
              </select>
            </div>
          </div>
        </div>

        <div class="hero__field">
          <label class="hero__field-label" for="heroMaxPrice">Faixa de preço</label>
          <div class="hero__field-inner">
            <svg class="hero__field-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="12" y1="1" x2="12" y2="23"/>
              <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
            </svg>
            <input type="text" id="heroMaxPrice" name="max_price" class="hero__field-input" placeholder="Ex: 3.500.000" inputmode="numeric">
          </div>
        </div>

        <button type="submit" class="hero__submit">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <circle cx="11" cy="11" r="8"/>
            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
          Buscar imóveis
        </button>
      </div>
    </form>
  </div>

  <div class="hero__scroll" aria-hidden="true">
    <span>Explore</span>
    <span class="hero__scroll-line"></span>
  </div>
</section>


<!-- ==================== CATEGORIAS ==================== -->
<section class="categories">
  <div class="container">
    <header class="section-header">
      <span class="section-header__eyebrow">Portfólio</span>
      <h2 class="section-header__title">Explore por <em>categoria</em></h2>
      <p class="section-header__subtitle">
        Selecione o tipo de imóvel que combina com seu estilo de vida
      </p>
    </header>

        <div class="categories__grid" data-stagger>

      <a href="<?= $base ?>/frontend/pages/imoveis.php?type=apartamento" class="category-card">
        <span class="category-card__index" aria-hidden="true">01</span>
        <span class="category-card__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="3" width="14" height="18" rx="1.5"/><path d="M9 7h.01M15 7h.01M9 11h.01M15 11h.01M9 15h.01M15 15h.01"/><path d="M10 21v-3h4v3"/></svg>
        </span>
        <h3 class="category-card__name">Apartamentos</h3>
        <p class="category-card__desc">Praticidade, segurança e localização privilegiada no coração da cidade.</p>
        <span class="category-card__cta">
          Ver imóveis
          <span class="category-card__cta-arrow" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
          </span>
        </span>
      </a>

      <a href="<?= $base ?>/frontend/pages/imoveis.php?type=casa" class="category-card">
        <span class="category-card__index" aria-hidden="true">02</span>
        <span class="category-card__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1z"/><path d="M9.5 21v-6.5h5V21"/></svg>
        </span>
        <h3 class="category-card__name">Casas</h3>
        <p class="category-card__desc">Espaço, privacidade e liberdade para viver cada ambiente do seu jeito.</p>
        <span class="category-card__cta">
          Ver imóveis
          <span class="category-card__cta-arrow" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
          </span>
        </span>
      </a>

      <a href="<?= $base ?>/frontend/pages/imoveis.php?type=cobertura" class="category-card">
        <span class="category-card__index" aria-hidden="true">03</span>
        <span class="category-card__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 21V9h14v12"/><path d="M3 21h18"/><path d="M8 9V5h8v4"/><path d="M10 2.5h4"/><path d="M9 13h6M9 17h6"/></svg>
        </span>
        <h3 class="category-card__name">Coberturas</h3>
        <p class="category-card__desc">Vista panorâmica, terraços amplos e o máximo em exclusividade.</p>
        <span class="category-card__cta">
          Ver imóveis
          <span class="category-card__cta-arrow" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
          </span>
        </span>
      </a>

      <a href="<?= $base ?>/frontend/pages/imoveis.php?type=terreno" class="category-card">
        <span class="category-card__index" aria-hidden="true">04</span>
        <span class="category-card__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="m3 7 6-3 6 3 6-3v13l-6 3-6-3-6 3z"/><path d="M9 4v13M15 7v13"/></svg>
        </span>
        <h3 class="category-card__name">Terrenos</h3>
        <p class="category-card__desc">Oportunidades para construir o projeto do zero, no lugar certo.</p>
        <span class="category-card__cta">
          Ver imóveis
          <span class="category-card__cta-arrow" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
          </span>
        </span>
      </a>
    </div>
  </div>
</section>

<!-- ==================== DESTAQUES ==================== -->
<section class="featured">
  <div class="container">
    <div class="featured__header">
      <header class="section-header section-header--left">
        <span class="section-header__eyebrow">Selecionados</span>
        <h2 class="section-header__title">Imóveis em <em>destaque</em></h2>
        <p class="section-header__subtitle">
          Curadoria semanal das melhores oportunidades do mercado
        </p>
      </header>

      <a href="<?= $base ?>/frontend/pages/imoveis.php" class="featured__link">
        Ver portfólio completo
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
          <line x1="5" y1="12" x2="19" y2="12"/>
          <polyline points="12 5 19 12 12 19"/>
        </svg>
      </a>
    </div>

    <div class="featured__grid" id="featuredGrid" data-stagger>
      <!-- Populado via JS -->
    </div>

    <div class="featured__grid" id="featuredSkeleton" hidden>
      <?php for ($i = 0; $i < 3; $i++): ?>
        <article class="property-card-skeleton">
          <div class="skeleton skeleton--image"></div>
          <div style="padding: 24px;">
            <div class="skeleton skeleton--title"></div>
            <div class="skeleton skeleton--text" style="width: 60%;"></div>
            <div class="skeleton skeleton--text" style="width: 80%;"></div>
          </div>
        </article>
      <?php endfor; ?>
    </div>
  </div>
</section>

<!-- ==================== SOBRE ==================== -->
<section class="about-section">
  <div class="container">
    <div class="about-section__grid">

      <div class="about-section__media" data-reveal="left">
        <img src="<?= $base ?>/frontend/images/about-bg.svg"
             alt="Curadoria DIOMAR_CS"
             loading="lazy"
             onerror="this.src='<?= $base ?>/frontend/images/Gemini_Generated_Image_.jfif'">
      </div>

      <div class="about-section__content" data-reveal="right">
        <span class="section-header__eyebrow">Sobre nós</span>
        <h2 class="about-section__title">
          Mais que uma imobiliária,<br>
          uma <em>curadoria de vidas</em>.
        </h2>

        <p class="about-section__text">
          Cada imóvel que apresentamos foi selecionado para se tornar
          o cenário de uma nova história. Trabalhamos com discrição,
          sofisticação e atenção obsessiva aos detalhes.
        </p>

        <ul class="about-section__list">
          <li class="about-section__list-item">Curadoria personalizada por perfil de vida</li>
          <li class="about-section__list-item">Assessoria jurídica e documental completa</li>
          <li class="about-section__list-item">Acompanhamento até a entrega das chaves</li>
          <li class="about-section__list-item">Atendimento dedicado por consultor especializado</li>
        </ul>

        <a href="<?= $base ?>/frontend/pages/sobre.php" class="btn btn-primary btn-lg">
          Conheça nossa história
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <line x1="5" y1="12" x2="19" y2="12"/>
            <polyline points="12 5 19 12 12 19"/>
          </svg>
        </a>
      </div>
    </div>
  </div>
</section>


<!-- ==================== DEPOIMENTO ==================== -->
<section class="quote-section">
  <div class="container">
    <div class="quote-section__inner" data-reveal>
      <svg class="quote-section__mark" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
        <path d="M6 17h3l2-4V7H5v6h3zm8 0h3l2-4V7h-6v6h3z"/>
      </svg>

      <p class="quote-section__text">
        A DIOMAR_CS não vende imóveis.
        Ela <em>conecta pessoas</em> aos cenários das suas próximas histórias.
      </p>

      <span class="quote-section__author">Filosofia da casa</span>
    </div>
  </div>
</section>

<!-- ==================== CTA FINAL ==================== -->
<section class="cta-section">
  <div class="container">
    <div class="cta-section__inner" data-reveal>
      <span class="cta-section__eyebrow">Curadoria exclusiva</span>

      <h2 class="cta-section__title">
        Você tem um imóvel <em>extraordinário</em>?
      </h2>

      <p class="cta-section__text">
        Nossa equipe de curadores apresenta seu imóvel a compradores
        selecionados, com toda a discrição que uma propriedade única merece.
      </p>

      <div class="cta-section__actions">
        <a href="<?= $base ?>/frontend/pages/anunciar.php" class="btn btn-gold btn-xl">
          Anunciar meu imóvel
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <line x1="5" y1="12" x2="19" y2="12"/>
            <polyline points="12 5 19 12 12 19"/>
          </svg>
        </a>
        <a href="<?= $base ?>/frontend/pages/contato.php" class="btn btn-outline btn-xl" style="color: #fff; border-color: rgba(255,255,255,0.4);">
          Falar com consultor
        </a>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/../components/footer.php'; ?>

<script>
  window.__HOME__ = true;
</script>