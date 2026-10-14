<?php
/**
 * Paginação.
 * @var int   $currentPage
 * @var int   $totalPages
 * @var array $queryParams  (preservar filtros)
 */
$currentPage = $currentPage ?? 1;
$totalPages  = $totalPages  ?? 1;
$queryParams = $queryParams ?? [];

if ($totalPages <= 1) return;

$buildUrl = function ($page) use ($queryParams) {
  $queryParams['page'] = $page;
  return '?' . http_build_query($queryParams);
};

$range = 2;
$start = max(1, $currentPage - $range);
$end   = min($totalPages, $currentPage + $range);
?>
<nav class="pagination" aria-label="Navegação de páginas">
  <a href="<?= $currentPage > 1 ? $buildUrl($currentPage - 1) : '#' ?>"
     class="pagination__item"
     <?= $currentPage <= 1 ? 'aria-disabled="true"' : '' ?>>
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
  </a>

  <?php if ($start > 1): ?>
    <a href="<?= $buildUrl(1) ?>" class="pagination__item">1</a>
    <?php if ($start > 2): ?><span class="pagination__item" aria-disabled="true">…</span><?php endif; ?>
  <?php endif; ?>

  <?php for ($i = $start; $i <= $end; $i++): ?>
    <a href="<?= $buildUrl($i) ?>"
       class="pagination__item<?= $i === $currentPage ? ' is-active' : '' ?>"
       <?= $i === $currentPage ? 'aria-current="page"' : '' ?>>
      <?= $i ?>
    </a>
  <?php endfor; ?>

  <?php if ($end < $totalPages): ?>
    <?php if ($end < $totalPages - 1): ?><span class="pagination__item" aria-disabled="true">…</span><?php endif; ?>
    <a href="<?= $buildUrl($totalPages) ?>" class="pagination__item"><?= $totalPages ?></a>
  <?php endif; ?>

  <a href="<?= $currentPage < $totalPages ? $buildUrl($currentPage + 1) : '#' ?>"
     class="pagination__item"
     <?= $currentPage >= $totalPages ? 'aria-disabled="true"' : '' ?>>
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
  </a>
</nav>