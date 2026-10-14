<?php
/**
 * Card reutilizável de imóvel.
 * @var array $property  Campos: id, code, title, slug, purpose, status, price,
 *                                 bedrooms, bathrooms, parking_spaces, area_built,
 *                                 neighborhood, city, main_image, featured, created_at
 */
$base       = '/diomar-cs';
$isNew      = isset($property['created_at']) && strtotime($property['created_at']) > strtotime('-15 days');
$imageUrl   = !empty($property['main_image'])
  ? $base . '/backend/uploads/properties/' . htmlspecialchars($property['main_image'])
  : $base . '/frontend/images/placeholder.jpg';
$priceLabel = $property['purpose'] === 'aluguel' ? 'Aluguel mensal' : 'Valor de venda';
?>
<article class="property-card" data-reveal data-property-id="<?= (int)$property['id'] ?>">
  <a href="<?= $base ?>/frontend/pages/imovel.php?id=<?= (int)$property['id'] ?>"
     class="property-card__link"
     aria-label="Ver detalhes: <?= htmlspecialchars($property['title']) ?>">

    <div class="property-card__image img-reveal">
      <img src="<?= $imageUrl ?>"
           alt="<?= htmlspecialchars($property['title']) ?>"
           loading="lazy"
           decoding="async">

      <div class="property-card__badges">
        <?php if (!empty($property['featured'])): ?>
          <span class="property-card__badge property-card__badge--featured">Destaque</span>
        <?php endif; ?>
        <span class="property-card__badge">
          <?= $property['purpose'] === 'venda' ? 'Venda' : 'Aluguel' ?>
        </span>
        <?php if ($isNew): ?>
          <span class="property-card__badge property-card__badge--new">Novo</span>
        <?php endif; ?>
      </div>

      <button type="button"
              class="property-card__fav"
              data-fav-id="<?= (int)$property['id'] ?>"
              aria-label="Adicionar aos favoritos">
        <svg width="18" height="18" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2"
             stroke-linecap="round" stroke-linejoin="round">
          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
      </button>

      <div class="property-card__actions">
        <button type="button" class="property-card__action-btn" data-action="share">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
          Compartilhar
        </button>
        <button type="button" class="property-card__action-btn" data-action="whatsapp">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347"/></svg>
          WhatsApp
        </button>
      </div>
    </div>

    <div class="property-card__body">
      <h3 class="property-card__title"><?= htmlspecialchars($property['title']) ?></h3>

      <p class="property-card__location">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
        <?= htmlspecialchars(trim(($property['neighborhood'] ?? '') . ($property['city'] ? ' — ' . $property['city'] : ''), ' —')) ?>
      </p>

      <ul class="property-card__meta">
        <?php if (!empty($property['bedrooms'])): ?>
          <li class="property-card__meta-item">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 4v16"/><path d="M2 8h18a2 2 0 0 1 2 2v10"/><path d="M2 17h20"/><path d="M6 8v9"/></svg>
            <?= (int)$property['bedrooms'] ?>
          </li>
        <?php endif; ?>
        <?php if (!empty($property['bathrooms'])): ?>
          <li class="property-card__meta-item">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 6 6.5 3.5a1.5 1.5 0 0 0-1-.5C4.683 3 4 3.683 4 4.5V17a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-5H4"/><line x1="10" y1="5" x2="8" y2="7"/><line x1="2" y1="12" x2="22" y2="12"/></svg>
            <?= (int)$property['bathrooms'] ?>
          </li>
        <?php endif; ?>
        <?php if (!empty($property['parking_spaces'])): ?>
          <li class="property-card__meta-item">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
            <?= (int)$property['parking_spaces'] ?>
          </li>
        <?php endif; ?>
        <?php if (!empty($property['area_built'])): ?>
          <li class="property-card__meta-item">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/><line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/></svg>
            <?= (int)$property['area_built'] ?> m²
          </li>
        <?php endif; ?>
      </ul>

      <div class="property-card__footer">
        <div class="property-card__price">
          <span class="property-card__price-label"><?= $priceLabel ?></span>
          <span class="property-card__price-value">
            R$ <?= number_format((float)$property['price'], 0, ',', '.') ?>
            <?php if ($property['purpose'] === 'aluguel'): ?><small>/mês</small><?php endif; ?>
          </span>
        </div>
        <span class="property-card__code">#<?= htmlspecialchars($property['code']) ?></span>
      </div>
    </div>
  </a>
</article>