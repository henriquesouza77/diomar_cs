<?php
$pageTitle       = 'Detalhes do imóvel';
$pageDescription = 'Veja todos os detalhes, fotos, valores e fale com o corretor responsável.';
$currentPage     = 'imoveis';
$loadPropertyDetails = true;

require_once __DIR__ . '/../components/header.php';
$base = '/diomar-cs';

// Validação do parâmetro id
$propertyId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
?>

<!-- ==================== CONTEÚDO ==================== -->
<section class="property-page" id="propertyPage" data-property-id="<?= $propertyId ?>">
  <div class="container">

    <!-- Estado de carregamento -->
    <div id="propertyLoading" class="empty-state" style="padding:var(--space-24) 0;">
      <div class="spinner spinner-lg"></div>
      <p class="empty-state__text" style="margin-top:var(--space-4);">Carregando imóvel…</p>
    </div>

    <!-- Estado de erro -->
    <div id="propertyError" class="empty-state" hidden>
      <div class="empty-state__icon" aria-hidden="true">⚠️</div>
      <h2 class="empty-state__title">Imóvel não encontrado</h2>
      <p class="empty-state__text">O imóvel que você procura pode ter sido removido ou não está mais disponível.</p>
      <a href="<?= $base ?>/frontend/pages/imoveis.php" class="btn btn-primary">Ver outros imóveis</a>
    </div>

    <!-- Conteúdo renderizado via JS -->
    <div id="propertyContent" hidden>

      <!-- Breadcrumb -->
      <nav class="breadcrumb" aria-label="Você está aqui" style="padding-top:var(--space-6);">
        <a href="<?= $base ?>/frontend/pages/index.php">Início</a>
        <span class="breadcrumb__separator">/</span>
        <a href="<?= $base ?>/frontend/pages/imoveis.php">Imóveis</a>
        <span class="breadcrumb__separator">/</span>
        <span id="breadcrumbTitle">—</span>
      </nav>

      <!-- Galeria -->
      <div class="property-gallery" id="gallery">
        <div class="property-gallery__main">
          <img id="galleryMain" src="" alt="" loading="eager">
          <button type="button" class="property-gallery__nav property-gallery__nav--prev" id="galleryPrev" aria-label="Imagem anterior">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
          </button>
          <button type="button" class="property-gallery__nav property-gallery__nav--next" id="galleryNext" aria-label="Próxima imagem">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
          </button>
          <button type="button" class="property-gallery__expand" id="galleryExpand">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/></svg>
            Ver todas
          </button>
          <span class="property-gallery__counter" id="galleryCounter">1 / 1</span>
        </div>
        <div class="property-gallery__thumbs" id="galleryThumbs"></div>
      </div>

      <div class="property-layout">

        <!-- ========== COLUNA PRINCIPAL ========== -->
        <div class="property-main">

          <header class="property-header">
            <div class="property-header__top">
              <div>
                <div class="property-header__badges" id="propertyBadges"></div>
                <h1 class="property-header__title" id="propertyTitle">—</h1>
                <div class="property-header__location">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                  <span id="propertyLocation">—</span>
                </div>
              </div>

              <div style="display:flex;flex-direction:column;gap:var(--space-3);align-items:flex-end;">
                <span class="property-header__code" id="propertyCode">#—</span>
                <div class="property-header__actions">
                  <button type="button" class="btn btn-secondary btn-sm" id="btnFavorite">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                    Favoritar
                  </button>
                  <button type="button" class="btn btn-secondary btn-sm" id="btnShare">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                    Compartilhar
                  </button>
                </div>
              </div>
            </div>

            <!-- Preço -->
            <div class="property-price-block">
              <div class="property-price-block__main">
                <span class="property-price-block__label" id="priceLabel">Valor de venda</span>
                <span class="property-price-block__value" id="propertyPrice">R$ —</span>
              </div>
              <div class="property-price-block__extras">
                <div class="property-price-block__extra">
                  <span class="property-price-block__extra-label">Condomínio</span>
                  <span class="property-price-block__extra-value" id="propertyCondo">R$ —</span>
                </div>
                <div class="property-price-block__extra">
                  <span class="property-price-block__extra-label">IPTU (anual)</span>
                  <span class="property-price-block__extra-value" id="propertyIptu">R$ —</span>
                </div>
              </div>
            </div>
          </header>

          <!-- Features principais -->
          <div class="property-main-features" id="propertyFeatures"></div>

          <!-- Descrição -->
          <section class="property-section">
            <h2 class="property-section__title">Sobre o imóvel</h2>
            <p class="property-description" id="propertyDescription">—</p>
          </section>

          <!-- Características adicionais -->
          <section class="property-section" id="propertyFeatureListWrapper" hidden>
            <h2 class="property-section__title">Características e comodidades</h2>
            <div class="property-features-grid" id="propertyFeatureList"></div>
          </section>

          <!-- Localização / mapa -->
          <section class="property-section">
            <h2 class="property-section__title">Localização</h2>
            <div class="property-map" id="propertyMap"></div>
          </section>
        </div>

        <!-- ========== SIDEBAR ========== -->
        <aside class="property-sidebar">

          <!-- Corretor -->
          <div class="agent-card" id="agentCard">
            <div class="agent-card__header">
              <div class="avatar avatar-lg" id="agentAvatar">—</div>
              <div class="agent-card__info">
                <h4 id="agentName">Corretor responsável</h4>
                <span class="agent-card__role">Corretor(a) DIOMAR_CS</span>
                <span class="agent-card__creci" id="agentCreci"></span>
              </div>
            </div>
            <div class="agent-card__contact">
              <a href="#" class="btn btn-whatsapp btn-block" id="btnWhatsapp" target="_blank" rel="noopener">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347"/></svg>
                Falar no WhatsApp
              </a>
              <a href="tel:" class="btn btn-outline btn-block" id="btnPhone">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                Ligar agora
              </a>
            </div>
          </div>

          <!-- Formulário de interesse -->
          <form class="interest-form" id="interestForm" aria-label="Formulário de interesse">
            <h3 class="interest-form__title">Tenho interesse</h3>
            <p class="interest-form__subtitle">Envie seus dados e retornaremos em breve.</p>

            <input type="hidden" name="property_id" id="interestPropertyId" value="<?= $propertyId ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

            <div class="form-group">
              <input type="text" name="name" class="form-input" placeholder="Seu nome completo" required>
            </div>
            <div class="form-group">
              <input type="email" name="email" class="form-input" placeholder="Seu e-mail" required>
            </div>
            <div class="form-group">
              <input type="tel" name="phone" class="form-input" placeholder="Telefone / WhatsApp" required>
            </div>
            <div class="form-group">
              <textarea name="message" class="form-textarea" rows="3" placeholder="Mensagem (opcional)"></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Enviar interesse</button>
            <p class="form-hint" style="margin-top:var(--space-3);text-align:center;">
              Ao enviar, você concorda com nossa <a href="#">política de privacidade</a>.
            </p>
          </form>

          <!-- Compartilhar -->
          <div class="share-block">
            <div class="share-block__label">Compartilhe este imóvel</div>
            <div class="share-block__buttons">
              <a href="#" class="share-btn share-btn--whatsapp" data-share="whatsapp" aria-label="Compartilhar no WhatsApp">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347"/></svg>
              </a>
              <a href="#" class="share-btn share-btn--facebook" data-share="facebook" aria-label="Compartilhar no Facebook">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
              </a>
              <a href="#" class="share-btn share-btn--twitter" data-share="twitter" aria-label="Compartilhar no Twitter">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg>
              </a>
              <button type="button" class="share-btn share-btn--copy" data-share="copy" aria-label="Copiar link">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
              </button>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </div>

  <!-- ==================== SEMELHANTES ==================== -->
  <section class="similar-properties" id="similarSection" hidden>
    <div class="container">
      <div class="section__header">
        <span class="section__eyebrow">Você também pode gostar</span>
        <h2 class="section__title">Imóveis semelhantes</h2>
      </div>
      <div class="properties-grid" id="similarGrid"></div>
    </div>
  </section>
</section>

<?php require __DIR__ . '/../components/footer.php'; ?>

<script>
  window.__PAGE__ = 'imovel';
</script>