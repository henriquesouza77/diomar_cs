<?php
$pageTitle       = 'Meus favoritos';
$pageDescription = 'Imóveis que você salvou.';
$currentPage     = 'favoritos';

require_once __DIR__ . '/../components/header.php';
$base = '/diomar-cs';
?>

<section class="properties-page" style="padding-top:calc(var(--navbar-height) + var(--space-12));">
  <div class="container">
    <div class="section__header" style="text-align:left;max-width:none;">
      <span class="section__eyebrow">Sua lista</span>
      <h1 class="section__title">Imóveis favoritos</h1>
      <p class="section__subtitle">Todos os imóveis que você salvou para depois.</p>
    </div>

    <div class="properties-grid" id="favoritesGrid"></div>
    <div class="properties-grid" id="favoritesSkeleton" hidden>
      <?php for ($i = 0; $i < 3; $i++) require __DIR__ . '/../components/property-card-skeleton.php'; ?>
    </div>

    <div id="favoritesEmpty" hidden>
      <?php
      $title   = 'Você ainda não tem favoritos';
      $text    = 'Explore nosso catálogo e clique no coração para salvar imóveis aqui.';
      $ctaText = 'Ver imóveis';
      $ctaHref = $base . '/frontend/pages/imoveis.php';
      $icon    = '❤️';
      require __DIR__ . '/../components/empty-state.php';
      ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/../components/footer.php'; ?>

<script>
  window.__PAGE__ = 'favoritos';
</script>