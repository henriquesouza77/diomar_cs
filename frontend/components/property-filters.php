<?php
/**
 * Componente: Filtros laterais da listagem de imóveis.
 * Usado em: frontend/pages/imoveis.php
 *
 * Preserva os valores enviados via $_GET.
 */
$getFilter = function ($key, $default = '') {
    return isset($_GET[$key]) ? htmlspecialchars((string)$_GET[$key]) : $default;
};

$purpose    = $getFilter('purpose');
$type       = $getFilter('type');
$city       = $getFilter('city');
$neighborhood = $getFilter('neighborhood');
$minPrice   = $getFilter('min_price');
$maxPrice   = $getFilter('max_price');
$bedrooms   = $getFilter('bedrooms');
$suites     = $getFilter('suites');
$bathrooms  = $getFilter('bathrooms');
$parking    = $getFilter('parking_spaces');
$minArea    = $getFilter('min_area');
?>
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
        <button type="button" class="tabs__item<?= $purpose === '' ? ' is-active' : '' ?>" data-purpose="">Todos</button>
        <button type="button" class="tabs__item<?= $purpose === 'venda' ? ' is-active' : '' ?>" data-purpose="venda">Comprar</button>
        <button type="button" class="tabs__item<?= $purpose === 'aluguel' ? ' is-active' : '' ?>" data-purpose="aluguel">Alugar</button>
        <span class="tabs__slider" aria-hidden="true"></span>
      </div>
    </div>
  </div>

  <!-- Localização -->
  <div class="filters__group">
    <div class="filters__group-title">Localização</div>
    <div class="filters__group-content">
      <input type="text" class="form-input" id="filterCity" placeholder="Cidade" value="<?= $city ?>">
      <input type="text" class="form-input" id="filterNeighborhood" placeholder="Bairro" value="<?= $neighborhood ?>" style="margin-top:8px;">
    </div>
  </div>

  <!-- Tipo -->
  <div class="filters__group">
    <div class="filters__group-title">Tipo de imóvel</div>
    <div class="filters__group-content">
      <select class="form-select" id="filterType">
        <option value="">Todos os tipos</option>
        <option value="apartamento"  <?= $type === 'apartamento' ? 'selected' : '' ?>>Apartamento</option>
        <option value="casa"         <?= $type === 'casa' ? 'selected' : '' ?>>Casa</option>
        <option value="cobertura"    <?= $type === 'cobertura' ? 'selected' : '' ?>>Cobertura</option>
        <option value="kitnet"       <?= $type === 'kitnet' ? 'selected' : '' ?>>Kitnet</option>
        <option value="studio"       <?= $type === 'studio' ? 'selected' : '' ?>>Studio</option>
        <option value="sala-comercial" <?= $type === 'sala-comercial' ? 'selected' : '' ?>>Sala Comercial</option>
        <option value="loja"         <?= $type === 'loja' ? 'selected' : '' ?>>Loja</option>
        <option value="galpao"       <?= $type === 'galpao' ? 'selected' : '' ?>>Galpão</option>
        <option value="sitio"        <?= $type === 'sitio' ? 'selected' : '' ?>>Sítio</option>
        <option value="fazenda"      <?= $type === 'fazenda' ? 'selected' : '' ?>>Fazenda</option>
        <option value="terreno"      <?= $type === 'terreno' ? 'selected' : '' ?>>Terreno</option>
        <option value="lote"         <?= $type === 'lote' ? 'selected' : '' ?>>Lote</option>
      </select>
    </div>
  </div>

  <!-- Preço -->
  <div class="filters__group">
    <div class="filters__group-title">Faixa de preço</div>
    <div class="filters__group-content">
      <div class="filters__range">
        <input type="text" class="form-input" id="filterMinPrice" placeholder="Mínimo" inputmode="numeric" value="<?= $minPrice ?>">
        <input type="text" class="form-input" id="filterMaxPrice" placeholder="Máximo" inputmode="numeric" value="<?= $maxPrice ?>">
      </div>
    </div>
  </div>

  <!-- Quartos -->
  <div class="filters__group">
    <div class="filters__group-title">Quartos</div>
    <div class="filters__group-content">
      <div class="filters__counter" id="filterBedrooms">
        <button type="button" class="counter-btn<?= $bedrooms === '' ? ' is-active' : '' ?>" data-value="">Todos</button>
        <button type="button" class="counter-btn<?= $bedrooms === '1' ? ' is-active' : '' ?>" data-value="1">1+</button>
        <button type="button" class="counter-btn<?= $bedrooms === '2' ? ' is-active' : '' ?>" data-value="2">2+</button>
        <button type="button" class="counter-btn<?= $bedrooms === '3' ? ' is-active' : '' ?>" data-value="3">3+</button>
        <button type="button" class="counter-btn<?= $bedrooms === '4' ? ' is-active' : '' ?>" data-value="4">4+</button>
      </div>
    </div>
  </div>

  <!-- Suítes -->
  <div class="filters__group">
    <div class="filters__group-title">Suítes</div>
    <div class="filters__group-content">
      <div class="filters__counter" id="filterSuites">
        <button type="button" class="counter-btn<?= $suites === '' ? ' is-active' : '' ?>" data-value="">Todas</button>
        <button type="button" class="counter-btn<?= $suites === '1' ? ' is-active' : '' ?>" data-value="1">1+</button>
        <button type="button" class="counter-btn<?= $suites === '2' ? ' is-active' : '' ?>" data-value="2">2+</button>
        <button type="button" class="counter-btn<?= $suites === '3' ? ' is-active' : '' ?>" data-value="3">3+</button>
      </div>
    </div>
  </div>

  <!-- Banheiros -->
  <div class="filters__group">
    <div class="filters__group-title">Banheiros</div>
    <div class="filters__group-content">
      <div class="filters__counter" id="filterBathrooms">
        <button type="button" class="counter-btn<?= $bathrooms === '' ? ' is-active' : '' ?>" data-value="">Todos</button>
        <button type="button" class="counter-btn<?= $bathrooms === '1' ? ' is-active' : '' ?>" data-value="1">1+</button>
        <button type="button" class="counter-btn<?= $bathrooms === '2' ? ' is-active' : '' ?>" data-value="2">2+</button>
        <button type="button" class="counter-btn<?= $bathrooms === '3' ? ' is-active' : '' ?>" data-value="3">3+</button>
      </div>
    </div>
  </div>

  <!-- Vagas -->
  <div class="filters__group">
    <div class="filters__group-title">Vagas</div>
    <div class="filters__group-content">
      <div class="filters__counter" id="filterParking">
        <button type="button" class="counter-btn<?= $parking === '' ? ' is-active' : '' ?>" data-value="">Todas</button>
        <button type="button" class="counter-btn<?= $parking === '1' ? ' is-active' : '' ?>" data-value="1">1+</button>
        <button type="button" class="counter-btn<?= $parking === '2' ? ' is-active' : '' ?>" data-value="2">2+</button>
        <button type="button" class="counter-btn<?= $parking === '3' ? ' is-active' : '' ?>" data-value="3">3+</button>
      </div>
    </div>
  </div>

  <!-- Área mínima -->
  <div class="filters__group">
    <div class="filters__group-title">Área mínima (m²)</div>
    <div class="filters__group-content">
      <input type="text" class="form-input" id="filterMinArea" placeholder="Ex: 60" inputmode="numeric" value="<?= $minArea ?>">
    </div>
  </div>
</aside>