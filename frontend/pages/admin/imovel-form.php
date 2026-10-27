<?php
$adminTitle  = 'Adicionar imóvel';
$adminActive = 'imoveis';

$propertyId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($propertyId) {
  $adminTitle = 'Editar imóvel';
}

require __DIR__ . '/_layout-top.php';
?>

<div class="admin-content">

  <div class="admin-page-header">
    <div>
      <h2 class="admin-page-header__title"><?= $propertyId ? 'Editar imóvel' : 'Adicionar imóvel' ?></h2>
      <p class="admin-page-header__subtitle">Preencha os dados abaixo com atenção.</p>
    </div>
    <div class="admin-page-header__actions">
      <a href="<?= $base ?>/frontend/pages/admin/imoveis.php" class="btn btn-ghost">Cancelar</a>
    </div>
  </div>

  <form class="property-form" id="propertyForm"
        data-property-id="<?= $propertyId ?>"
        enctype="multipart/form-data"
        novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
    <input type="hidden" name="id" value="<?= $propertyId ?>">

    <!-- ========== 1. Informações ========== -->
    <section class="form-section">
      <div class="form-section__header">
        <span class="form-section__number">1</span>
        <h3 class="form-section__title">Informações principais</h3>
      </div>

      <div class="form-grid form-grid--2">
        <div class="form-group">
          <label class="form-label" for="propCode">Código</label>
          <input type="text" id="propCode" name="code" class="form-input" placeholder="Gerado automaticamente ao salvar" readonly>
          <small class="form-hint">O código é gerado automaticamente e não pode ser alterado.</small>
        </div>

        <div class="form-group">
          <label class="form-label" for="propPurpose">Finalidade <span class="required">*</span></label>
          <select id="propPurpose" name="purpose" class="form-select" required>
            <option value="venda">Venda</option>
            <option value="aluguel">Aluguel</option>
          </select>
        </div>

        <div class="form-group form-grid__full">
          <label class="form-label" for="propTitle">Título do anúncio <span class="required">*</span></label>
          <input type="text" id="propTitle" name="title" class="form-input" placeholder="Ex: Apartamento 3 quartos com vista para o parque" required>
        </div>

        <div class="form-group form-grid__full">
          <label class="form-label" for="propDescription">Descrição</label>
          <textarea id="propDescription" name="description" class="form-textarea" rows="6" placeholder="Descreva o imóvel em detalhes…"></textarea>
        </div>

        <div class="form-group">
          <label class="form-label" for="propCategory">Categoria <span class="required">*</span></label>
          <select id="propCategory" name="category_id" class="form-select" required>
            <option value="">Selecione</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label" for="propType">Tipo de imóvel <span class="required">*</span></label>
          <select id="propType" name="property_type_id" class="form-select" required>
            <option value="">Selecione</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label" for="propStatus">Status</label>
          <select id="propStatus" name="status" class="form-select">
            <option value="disponivel">Disponível</option>
            <option value="reservado">Reservado</option>
            <option value="vendido">Vendido</option>
            <option value="alugado">Alugado</option>
            <option value="inativo">Inativo</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label" for="propPrice">Preço (R$) <span class="required">*</span></label>
          <input type="text" id="propPrice" name="price" class="form-input" inputmode="numeric" required>
        </div>

        <div class="form-options form-grid__full">
          <label class="form-option">
            <input type="checkbox" name="featured" value="1">
            <span class="form-option__text">
              <strong>Marcar como destaque</strong>
              <small>Aparece em "Imóveis em destaque" na página inicial.</small>
            </span>
          </label>

          <label class="form-option">
            <input type="checkbox" name="published" value="1" checked>
            <span class="form-option__text">
              <strong>Publicar no site</strong>
              <small>Visível para os visitantes. Desmarque para manter como rascunho.</small>
            </span>
          </label>
        </div>
      </div>
    </section>

    <!-- ========== 2. Localização ========== -->
    <section class="form-section">
      <div class="form-section__header">
        <span class="form-section__number">2</span>
        <h3 class="form-section__title">Localização</h3>
      </div>

      <div class="form-grid form-grid--3">
        <div class="form-group">
          <label class="form-label" for="propZipcode">CEP</label>
          <input type="text" id="propZipcode" name="zipcode" class="form-input" placeholder="00000-000" inputmode="numeric">
        </div>

        <div class="form-group form-grid__span-2">
          <label class="form-label" for="propStreet">Endereço</label>
          <input type="text" id="propStreet" name="street" class="form-input">
        </div>

        <div class="form-group">
          <label class="form-label" for="propNumber">Número</label>
          <input type="text" id="propNumber" name="number" class="form-input">
        </div>

        <div class="form-group">
          <label class="form-label" for="propComplement">Complemento</label>
          <input type="text" id="propComplement" name="complement" class="form-input">
        </div>

        <div class="form-group">
          <label class="form-label" for="propNeighborhood">Bairro</label>
          <input type="text" id="propNeighborhood" name="neighborhood" class="form-input">
        </div>

        <div class="form-group">
          <label class="form-label" for="propCity">Cidade <span class="required">*</span></label>
          <input type="text" id="propCity" name="city" class="form-input" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="propState">Estado <span class="required">*</span></label>
          <input type="text" id="propState" name="state" class="form-input" maxlength="2" placeholder="SP" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="propLat">Latitude</label>
          <input type="text" id="propLat" name="latitude" class="form-input" placeholder="-23.5505">
        </div>

        <div class="form-group">
          <label class="form-label" for="propLng">Longitude</label>
          <input type="text" id="propLng" name="longitude" class="form-input" placeholder="-46.6333">
        </div>
      </div>
    </section>

    <!-- ========== 3. Características ========== -->
    <section class="form-section">
      <div class="form-section__header">
        <span class="form-section__number">3</span>
        <h3 class="form-section__title">Características</h3>
      </div>

      <div class="form-grid form-grid--4">
        <div class="form-group">
          <label class="form-label" for="propAreaTotal">Área total (m²)</label>
          <input type="text" id="propAreaTotal" name="area_total" class="form-input" inputmode="numeric">
        </div>

        <div class="form-group">
          <label class="form-label" for="propAreaBuilt">Área construída (m²)</label>
          <input type="text" id="propAreaBuilt" name="area_built" class="form-input" inputmode="numeric">
        </div>

        <div class="form-group">
          <label class="form-label" for="propBedrooms">Quartos</label>
          <input type="number" id="propBedrooms" name="bedrooms" class="form-input" min="0">
        </div>

        <div class="form-group">
          <label class="form-label" for="propSuites">Suítes</label>
          <input type="number" id="propSuites" name="suites" class="form-input" min="0">
        </div>

        <div class="form-group">
          <label class="form-label" for="propBathrooms">Banheiros</label>
          <input type="number" id="propBathrooms" name="bathrooms" class="form-input" min="0">
        </div>

        <div class="form-group">
          <label class="form-label" for="propParking">Vagas</label>
          <input type="number" id="propParking" name="parking_spaces" class="form-input" min="0">
        </div>

        <div class="form-group">
          <label class="form-label" for="propFloor">Andar</label>
          <input type="number" id="propFloor" name="floor" class="form-input" min="0">
        </div>

        <div class="form-group">
          <label class="form-label" for="propCondo">Condomínio (R$)</label>
          <input type="text" id="propCondo" name="condo_fee" class="form-input" inputmode="numeric">
        </div>

        <div class="form-group">
          <label class="form-label" for="propIptu">IPTU (R$ anual)</label>
          <input type="text" id="propIptu" name="iptu" class="form-input" inputmode="numeric">
        </div>
      </div>
    </section>

    <!-- ========== 4. Características adicionais ========== -->
    <section class="form-section">
      <div class="form-section__header">
        <span class="form-section__number">4</span>
        <h3 class="form-section__title">
          Comodidades e características extras
          <span class="image-counter" id="featuresCounter">0 selecionadas</span>
        </h3>
      </div>

      <div class="filters__chips" id="featuresList" data-stagger>
        <!-- Carregado via JS -->
      </div>
    </section>

    <!-- ========== 5. Fotos ========== -->
    <section class="form-section">
      <div class="form-section__header">
        <span class="form-section__number">5</span>
        <h3 class="form-section__title">
          Fotos do imóvel
          <span class="image-counter" id="imageCounter">0 fotos</span>
        </h3>
      </div>

      <div class="image-uploader" id="imageUploader">
        <label class="image-uploader__drop" for="imageInput">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
            <circle cx="8.5" cy="8.5" r="1.5"/>
            <polyline points="21 15 16 10 5 21"/>
          </svg>
          <span>Arraste as fotos aqui ou clique para escolher</span>
          <small>JPG, PNG ou WebP · até 5 MB cada · a primeira vira a capa</small>
        </label>
        <input type="file" id="imageInput" name="images[]" accept="image/*" multiple style="display: none;">
      </div>

      <p class="form-hint" style="margin-top: 12px;">
        💡 <strong>A primeira foto será a capa.</strong>
        Toque na estrela ⭐ para escolher outra como principal.
      </p>
    </section>

    <!-- ========== Ações ========== -->
    <div class="admin-form__actions">
      <a href="<?= $base ?>/frontend/pages/admin/imoveis.php" class="btn btn-ghost">Cancelar</a>
      <button type="submit" class="btn btn-primary btn-lg">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        <?= $propertyId ? 'Salvar alterações' : 'Cadastrar imóvel' ?>
      </button>
    </div>
  </form>
</div>

<?php
// ⚠️ IMPORTANTE: declara a section ANTES de carregar admin.js
// e o admin.js deve ser carregado DENTRO do _layout-bottom.php
?>
<script>
  window.__ADMIN__ = 'imovel-form';
  window.__PROPERTY_ID__ = <?= (int)$propertyId ?>;
</script>

<?php require __DIR__ . '/_layout-bottom.php'; ?>