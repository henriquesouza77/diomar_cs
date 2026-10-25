<?php
$pageTitle       = 'Sobre nós';
$pageDescription = 'Conheça a história, valores e filosofia da DIOMAR_CS IMÓVEIS.';
$currentPage     = 'sobre';

require_once __DIR__ . '/../components/header.php';

$base = BASE_PATH;
?>

<!-- ==================== HERO ==================== -->
<section class="properties-hero">
  <div class="container properties-hero__inner">

    <nav class="breadcrumb" aria-label="Você está aqui">
      <a href="<?= $base ?>/frontend/pages/index.php">Início</a>
      <span class="breadcrumb__separator">/</span>
      <span>Sobre nós</span>
    </nav>

    <h1>A história por trás dos <em>imóveis</em></h1>
    <p>
      Mais que uma imobiliária, uma curadoria dedicada a conectar pessoas
      aos cenários das suas próximas histórias.
    </p>
  </div>
</section>

<!-- ==================== SEÇÃO SOBRE ==================== -->
<section class="about-section">
  <div class="container">
    <div class="about-section__grid">

      <div class="about-section__media" data-reveal="left">
        <img src="<?= $base ?>/frontend/images/about-bg.svg"
             alt="Curadoria DIOMAR_CS"
             loading="lazy"
             onerror="this.src='<?= $base ?>/frontend/images/placeholder.svg'">

        <div class="about-section__badge">
          <div class="about-section__badge-value">
            15<small>anos</small>
          </div>
          <div class="about-section__badge-label">realizando sonhos</div>
        </div>
      </div>

      <div class="about-section__content" data-reveal="right">
        <span class="section-header__eyebrow">Nossa história</span>
        <h2 class="about-section__title">
          Mais que imóveis,<br>
          <em>realizamos histórias</em>.
        </h2>

        <p class="about-section__text">
          Fundada com o propósito de transformar a forma como as pessoas
          encontram seu lar, a DIOMAR_CS IMÓVEIS se consolidou como referência
          em atendimento de alto padrão.
        </p>

        <p class="about-section__text">
          Trabalhamos com uma curadoria criteriosa de imóveis e um time de
          corretores experientes, prontos para entender suas necessidades
          e oferecer a melhor solução.
        </p>

        <ul class="about-section__list">
          <li class="about-section__list-item">
            <strong>Missão:</strong> conectar pessoas aos seus lares com transparência
          </li>
          <li class="about-section__list-item">
            <strong>Visão:</strong> ser referência em atendimento imobiliário personalizado
          </li>
          <li class="about-section__list-item">
            <strong>Valores:</strong> ética, confiança, excelência e proximidade
          </li>
        </ul>

        <a href="<?= $base ?>/frontend/pages/contato.php" class="btn btn-primary btn-lg">
          Fale com um consultor
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <line x1="5" y1="12" x2="19" y2="12"/>
            <polyline points="12 5 19 12 12 19"/>
          </svg>
        </a>
      </div>
    </div>
  </div>
</section>

<!-- ==================== NÚMEROS ==================== -->
<section class="numbers-section">
  <div class="container">
    <div class="numbers-section__grid" data-stagger>
      <div class="numbers-section__item">
        <div class="numbers-section__value" data-counter="15">0</div>
        <div class="numbers-section__label">Anos de mercado</div>
      </div>
      <div class="numbers-section__item">
        <div class="numbers-section__value" data-counter="1200">0</div>
        <div class="numbers-section__label">Imóveis negociados</div>
      </div>
      <div class="numbers-section__item">
        <div class="numbers-section__value" data-counter="980">0</div>
        <div class="numbers-section__label">Clientes satisfeitos</div>
      </div>
      <div class="numbers-section__item">
        <div class="numbers-section__value" data-counter="98">0<small>%</small></div>
        <div class="numbers-section__label">Índice de recomendação</div>
      </div>
    </div>
  </div>
</section>

<!-- ==================== VALORES / DIFERENCIAIS ==================== -->
<section class="categories">
  <div class="container">
    <header class="section-header">
      <span class="section-header__eyebrow">Nossos diferenciais</span>
      <h2 class="section-header__title">Por que <em>nos escolher</em></h2>
      <p class="section-header__subtitle">
        Cada detalhe pensado para oferecer uma experiência imobiliária superior
      </p>
    </header>

    <div class="categories__grid" data-stagger>
      <div class="category-card">
        <span class="category-card__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2L15 9L22 10L17 15L18 22L12 19L6 22L7 15L2 10L9 9Z"/>
          </svg>
        </span>
        <h3 class="category-card__name">Curadoria exclusiva</h3>
        <span class="category-card__count">Seleção criteriosa de imóveis</span>
      </div>

      <div class="category-card">
        <span class="category-card__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
        </span>
        <h3 class="category-card__name">Atendimento dedicado</h3>
        <span class="category-card__count">Consultor pessoal em cada etapa</span>
      </div>

      <div class="category-card">
        <span class="category-card__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
          </svg>
        </span>
        <h3 class="category-card__name">Segurança jurídica</h3>
        <span class="category-card__count">Assessoria documental completa</span>
      </div>

      <div class="category-card">
        <span class="category-card__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <polyline points="12 6 12 12 16 14"/>
          </svg>
        </span>
        <h3 class="category-card__name">Agilidade real</h3>
        <span class="category-card__count">Resposta em até 24 horas</span>
      </div>
    </div>
  </div>
</section>

<!-- ==================== QUOTE ==================== -->
<section class="quote-section">
  <div class="container">
    <div class="quote-section__inner" data-reveal>
      <svg class="quote-section__mark" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
        <path d="M6 17h3l2-4V7H5v6h3zm8 0h3l2-4V7h-6v6h3z"/>
      </svg>

      <p class="quote-section__text">
        Encontrar um imóvel é técnico.<br>
        Encontrar um <em>lar</em> é uma arte que levamos a sério.
      </p>

      <span class="quote-section__author">Nossa filosofia</span>
    </div>
  </div>
</section>

<!-- ==================== CTA FINAL ==================== -->
<section class="cta-section">
  <div class="container">
    <div class="cta-section__inner" data-reveal>
      <span class="cta-section__eyebrow">Vamos conversar</span>

      <h2 class="cta-section__title">
        Pronto para encontrar seu <em>próximo endereço</em>?
      </h2>

      <p class="cta-section__text">
        Nossa equipe está pronta para oferecer uma consultoria personalizada
        e encontrar o imóvel que combina com seu estilo de vida.
      </p>

      <div class="cta-section__actions">
        <a href="<?= $base ?>/frontend/pages/contato.php" class="btn btn-gold btn-xl">
          Falar com consultor
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <line x1="5" y1="12" x2="19" y2="12"/>
            <polyline points="12 5 19 12 12 19"/>
          </svg>
        </a>
        <a href="<?= $base ?>/frontend/pages/imoveis.php" class="btn btn-outline btn-xl" style="color: #fff; border-color: rgba(255,255,255,0.4);">
          Ver portfólio
        </a>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/../components/footer.php'; ?>

<script>
  window.__PAGE__ = 'sobre';
</script>