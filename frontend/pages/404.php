<?php
$pageTitle   = 'Página não encontrada';
$currentPage = '404';
require __DIR__ . '/../components/header.php';
$base = '/diomar-cs';
?>

<section class="section" style="padding-top:calc(var(--navbar-height) + var(--space-24));min-height:80vh;">
  <div class="container">
    <div class="empty-state" style="padding:var(--space-20) 0;">
      <div class="empty-state__icon" aria-hidden="true" style="font-family:var(--font-display);font-size:4rem;">404</div>
      <h1 class="empty-state__title" style="font-family:var(--font-display);font-size:var(--fs-4xl);">Página não encontrada</h1>
      <p class="empty-state__text">O endereço que você procura pode ter sido movido ou não existe mais.</p>
      <div style="display:flex;gap:var(--space-3);justify-content:center;flex-wrap:wrap;">
        <a href="<?= $base ?>/frontend/pages/index.php" class="btn btn-primary">Voltar ao início</a>
        <a href="<?= $base ?>/frontend/pages/imoveis.php" class="btn btn-secondary">Ver imóveis</a>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/../components/footer.php'; ?>