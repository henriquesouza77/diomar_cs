<?php
/**
 * Estado vazio reutilizável.
 * @var string $title
 * @var string $text
 * @var string $ctaText
 * @var string $ctaHref
 * @var string $icon  (emoji ou path SVG)
 */
$title   = $title   ?? 'Nenhum resultado encontrado';
$text    = $text    ?? 'Tente ajustar os filtros ou volte mais tarde.';
$ctaText = $ctaText ?? null;
$ctaHref = $ctaHref ?? '#';
$icon    = $icon    ?? '🔍';
?>
<div class="empty-state" role="status">
  <div class="empty-state__icon" aria-hidden="true"><?= $icon ?></div>
  <h3 class="empty-state__title"><?= htmlspecialchars($title) ?></h3>
  <p class="empty-state__text"><?= htmlspecialchars($text) ?></p>
  <?php if ($ctaText): ?>
    <a href="<?= htmlspecialchars($ctaHref) ?>" class="btn btn-primary"><?= htmlspecialchars($ctaText) ?></a>
  <?php endif; ?>
</div>