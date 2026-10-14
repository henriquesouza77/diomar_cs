<?php
$pageTitle       = 'Imóveis';
$pageDescription = 'Encontre apartamentos, casas, terrenos e muito mais em nossa curadoria premium.';
$currentPage     = 'imoveis';

require_once __DIR__ . '/../components/header.php';
$base = '/diomar-cs';
?>

<!-- ==================== HERO DA PÁGINA ==================== -->
<section class="properties-hero">
  <div class="container properties-hero__inner">
    <nav class="breadcrumb" aria-label="Você está aqui" style="color:rgba(255,255,255,0.7);">
      <a href="<?= $base ?>/frontend/pages/index.php" style="color:inherit;">Início</a>
      <span class="breadcrumb__separator">/</span>
      <span>Imóveis</span>
    </nav>

    <h1>Encontre seu próximo imóvel</h1>
    <p id="resultCount" aria-live="polite">Carregando imóveis…</p>
  </div>
</section>

<!-- ==================== LISTAGEM ==================== -->
<section class="container">
  <div class="properties-layout">

    <!-- ========== FILTROS ========== -->
    <aside class="filters" id="filtersPanel" aria-label="Filtros de busca">
      <div class="filters__header">
        <h2 class="filters__title">Filtros</h2>
        <button type="button" class="filters__clear" id="clearFilters">Limpar</button>
      </div>

      <!-- Finalidade -->
      <div class="filters__group">
        <div class="filters__group-title">Finalidade</div>
        <div class="filters__group-content">
          <div class="tabs" id="purposeTabs">
            <button type="button" class="tabs__item is-active" data-purpose="">Todos</button>
            <button type="button" class="tabs__item" data-purpose="venda">Comprar</button>
            <button type="button" class="tabs__item" data-purpose="aluguel">Alugar</button>
            <span class="tabs__slider" aria-hidden="true"></span>
          </div>
        </div>
      </div>

      <!-- Localização -->
      <div class="filters__group">
        <div class="filters__group-title">Localização</div>
        <div class="filters__group-content">
          <input type="text" class="form-input" id="filterCity" placeholder="Cidade">
          <input type="text" class="form-input" id="filterNeighborhood" placeholder="Bairro" style="margin-top:8px;">
        </div>
      </div>

      <!-- Tipo -->
      <div class="filters__group">
        <div class="filters__group-title">Tipo de imóvel</div>
        <div class="filters__group-content">
          <select class="form-select" id="filterType">
            <option value="">Todos os tipos</option>
            <option value="apartamento">Apartamento</option>
            <option value="casa">Casa</option>
            <option value="cobertura">Cobertura</option>
            <option value="kitnet">Kitnet</option>
            <option value="studio">Studio</option>
            <option value="sala-comercial">Sala Comercial</option>
            <option value="loja">Loja</option>
            <option value="galpao">Galpão</option>
            <option value="sitio">Sítio</option>
            <option value="fazenda">Fazenda</option>
            <option value="terreno">Terreno</option>
            <option value="lote">Lote</option>
          </select>
        </div>
      </div>

      <!-- Preço -->
      <div class="filters__group">
        <div class="filters__group-title">Faixa de preço</div>
        <div class="filters__group-content">
          <div class="filters__range">
            <input type="text" class="form-input" id="filterMinPrice" placeholder="Mínimo" inputmode="numeric">
            <input type="text" class="form-input" id="filterMaxPrice" placeholder="Máximo" inputmode="numeric">
          </div>
        </div>
      </div>

      <!-- Quartos -->
      <div class="filters__group">
        <div class="filters__group-title">Quartos</div>
        <div class="filters__group-content">
          <div class="filters__counter" id="filterBedrooms">
            <button type="button" class="counter-btn is-active" data-value="">Todos</button>
            <button type="button" class="counter-btn" data-value="1">1+</button>
            <button type="button" class="counter-btn" data-value="2">2+</button>
            <button type="button" class="counter-btn" data-value="3">3+</button>
            <button type="button" class="counter-btn" data-value="4">4+</button>
          </div>
        </div>
      </div>

      <!-- Suítes -->
      <div class="filters__group">
        <div class="filters__group-title">Suítes</div>
        <div class="filters__group-content">
          <div class="filters__counter" id="filterSuites">
            <button type="button" class="counter-btn is-active" data-value="">Todas</button>
            <button type="button" class="counter-btn" data-value="1">1+</button>
            <button type="button" class="counter-btn" data-value="2">2+</button>
            <button type="button" class="counter-btn" data-value="3">3+</button>
          </div>
        </div>
      </div>

      <!-- Banheiros -->
      <div class="filters__group">
        <div class="filters__group-title">Banheiros</div>
        <div class="filters__group-content">
          <div class="filters__counter" id="filterBathrooms">
            <button type="button" class="counter-btn is-active" data-value="">Todos</button>
            <button type="button" class="counter-btn" data-value="1">1+</button>
            <button type="button" class="counter-btn" data-value="2">2+</button>
            <button type="button" class="counter-btn" data-value="3">3+</button>
          </div>
        </div>
      </div>

      <!-- Vagas -->
      <div class="filters__group">
        <div class="filters__group-title">Vagas</div>
        <div class="filters__group-content">
          <div class="filters__counter" id="filterParking">
            <button type="button" class="counter-btn is-active" data-value="">Todas</button>
            <button type="button" class="counter-btn" data-value="1">1+</button>
            <button type="button" class="counter-btn" data-value="2">2+</button>
            <button type="button" class="counter-btn" data-value="3">3+</button>
          </div>
        </div>
      </div>

      <!-- Área mínima -->
      <div class="filters__group">
        <div class="filters__group-title">Área mínima (m²)</div>
        <div class="filters__group-content">
          <input type="text" class="form-input" id="filterMinArea" placeholder="Ex: 60" inputmode="numeric">
        </div>
      </div>
    </aside>

    <!-- ========== CONTEÚDO ========== -->
    <div class="properties-content">

      <div class="properties-toolbar">
        <span class="properties-toolbar__count" id="toolbarCount">
          <strong>—</strong> imóveis encontrados
        </span>

        <div class="properties-toolbar__actions">
          <select class="form-select" id="sortBy" aria-label="Ordenar por">
            <option value="recent">Mais recentes</option>
            <option value="price_asc">Menor preço</option>
            <option value="price_desc">Maior preço</option>
            <option value="area_desc">Maior área</option>
          </select>

          <div class="view-switch" role="group" aria-label="Visualização">
            <button type="button" class="view-switch__btn is-active" data-view="grid" aria-label="Grade">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            </button>
            <button type="button" class="view-switch__btn" data-view="list" aria-label="Lista">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
            </button>
          </div>
        </div>
      </div>

      <div class="active-filters" id="activeFilters"></div>

      <div class="properties-grid" id="propertiesGrid">
        <!-- Preenchido via JS -->
      </div>

      <!-- Skeleton -->
      <div class="properties-grid" id="propertiesSkeleton" hidden>
        <?php for ($i = 0; $i < 6; $i++) require __DIR__ . '/../components/property-card-skeleton.php'; ?>
      </div>

      <div id="paginationContainer"></div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/../components/footer.php'; ?>

<script>
  window.__PAGE__ = 'imoveis';
</script>