<?php
$pageTitle       = 'Anunciar imóvel';
$pageDescription = 'Anuncie seu imóvel com a DIOMAR_CS IMÓVEIS e alcance compradores qualificados.';
$currentPage     = 'anunciar';

require_once __DIR__ . '/../components/header.php';

$base = '/diomar-cs';
?>

<!-- ==================== HERO ==================== -->
<section class="properties-hero">
  <div class="container properties-hero__inner">

    <nav class="breadcrumb" aria-label="Você está aqui">
      <a href="<?= $base ?>/frontend/pages/index.php">Início</a>
      <span class="breadcrumb__separator">/</span>
      <span>Anunciar imóvel</span>
    </nav>

    <h1>Anuncie seu imóvel com <em>quem entende</em></h1>
    <p>
      Alcance compradores qualificados e venda ou alugue com toda a
      assessoria que uma propriedade única merece.
    </p>
  </div>
</section>

<!-- ==================== COMO FUNCIONA ==================== -->
<section class="categories">
  <div class="container">
    <header class="section-header">
      <span class="section-header__eyebrow">Como funciona</span>
      <h2 class="section-header__title">Simples, rápido e <em>sem burocracia</em></h2>
      <p class="section-header__subtitle">
        4 passos para conectar seu imóvel aos compradores certos
      </p>
    </header>

    <div class="categories__grid" data-stagger>

      <div class="category-card" style="cursor: default;">
        <span class="category-card__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
        </span>
        <h3 class="category-card__name">1. Fale conosco</h3>
        <span class="category-card__count">Conte sobre o seu imóvel</span>
      </div>

      <div class="category-card" style="cursor: default;">
        <span class="category-card__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="1" x2="12" y2="23"/>
            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
          </svg>
        </span>
        <h3 class="category-card__name">2. Avaliação gratuita</h3>
        <span class="category-card__count">Valor de mercado justo</span>
      </div>

      <div class="category-card" style="cursor: default;">
        <span class="category-card__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="18" height="18" rx="2"/>
            <circle cx="8.5" cy="8.5" r="1.5"/>
            <polyline points="21 15 16 10 5 21"/>
          </svg>
        </span>
        <h3 class="category-card__name">3. Publicação premium</h3>
        <span class="category-card__count">Fotos profissionais inclusas</span>
      </div>

      <div class="category-card" style="cursor: default;">
        <span class="category-card__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
        </span>
        <h3 class="category-card__name">4. Negociação assistida</h3>
        <span class="category-card__count">Acompanhamento completo</span>
      </div>
    </div>
  </div>
</section>

<!-- ==================== FORMULÁRIO ==================== -->
<section class="section" style="background: var(--bg-secundario);">
  <div class="container container-narrow">

    <div style="text-align: center; margin-bottom: 48px;" data-reveal>
      <span class="section-header__eyebrow" style="display: inline-block;">Vamos começar</span>
      <h2 class="section-header__title" style="margin: 12px 0;">Conte sobre o seu imóvel</h2>
      <p class="section-header__subtitle">
        Preencha os dados abaixo e retornaremos em até 24 horas com uma
        avaliação personalizada.
      </p>
    </div>

    <form class="card" style="padding: clamp(24px, 4vw, 48px);" id="anunciarForm" novalidate data-reveal>
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

      <div class="form-row form-row--2">
        <div class="field" data-field>
          <input type="text" id="anName" name="name" class="field__input" placeholder=" " required>
          <label for="anName" class="field__label">Nome completo *</label>
        </div>

        <div class="field" data-field>
          <input type="tel" id="anPhone" name="phone" class="field__input" placeholder=" " required>
          <label for="anPhone" class="field__label">Telefone / WhatsApp *</label>
        </div>
      </div>

      <div class="field" data-field>
        <input type="email" id="anEmail" name="email" class="field__input" placeholder=" " required>
        <label for="anEmail" class="field__label">E-mail *</label>
      </div>

      <div class="form-row form-row--2">
        <div class="field" data-field>
          <select id="anType" name="property_type" class="field__select" required>
            <option value="">Selecione</option>
            <option value="apartamento">Apartamento</option>
            <option value="casa">Casa</option>
            <option value="cobertura">Cobertura</option>
            <option value="terreno">Terreno</option>
            <option value="comercial">Comercial</option>
          </select>
          <label for="anType" class="field__label">Tipo de imóvel *</label>
        </div>

        <div class="field" data-field>
          <select id="anPurpose" name="purpose" class="field__select" required>
            <option value="venda">Vender</option>
            <option value="aluguel">Alugar</option>
          </select>
          <label for="anPurpose" class="field__label">Finalidade *</label>
        </div>
      </div>

      <div class="field" data-field>
        <textarea id="anMessage" name="message" class="field__textarea" placeholder=" " rows="5"></textarea>
        <label for="anMessage" class="field__label">
          Detalhes do imóvel (localização, metragem, quartos, etc.)
        </label>
      </div>

      <button type="submit" class="btn btn-primary btn-lg btn-block" style="margin-top: 16px;">
        Enviar proposta
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
          <line x1="5" y1="12" x2="19" y2="12"/>
          <polyline points="12 5 19 12 12 19"/>
        </svg>
      </button>

      <p style="text-align: center; margin-top: 16px; font-size: 13px; color: var(--cor-texto-muted);">
        Ao enviar, você concorda com nossa
        <a href="#" style="color: var(--cor-primaria); text-decoration: underline;">política de privacidade</a>.
      </p>
    </form>
  </div>
</section>

<!-- ==================== CTA ==================== -->
<section class="cta-section">
  <div class="container">
    <div class="cta-section__inner" data-reveal>
      <span class="cta-section__eyebrow">Dúvidas?</span>

      <h2 class="cta-section__title">
        Prefere conversar com um <em>consultor</em>?
      </h2>

      <p class="cta-section__text">
        Nossa equipe está disponível para tirar todas as suas dúvidas
        e orientar o melhor caminho para o seu imóvel.
      </p>

      <div class="cta-section__actions">
        <a href="https://wa.me/5511939565958" target="_blank" rel="noopener" class="btn btn-whatsapp btn-xl">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347"/>
          </svg>
          Falar no WhatsApp
        </a>
        <a href="<?= $base ?>/frontend/pages/contato.php" class="btn btn-outline btn-xl" style="color: #fff; border-color: rgba(255,255,255,0.4);">
          Ver contatos
        </a>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/../components/footer.php'; ?>

<script>
  window.__PAGE__ = 'anunciar';
</script>