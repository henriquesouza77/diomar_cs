<?php
$pageTitle       = 'Sem conexão';
$pageDescription = 'Você está offline.';
$currentPage     = 'offline';
$skipNavbar      = true;

require_once __DIR__ . '/../components/header.php';
$base = '/diomar-cs';
?>
<main class="offline-page">
  <div class="container">
    <div class="offline-page__inner">

      <div class="offline-page__icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M1 1l22 22"/>
          <path d="M16.72 11.06A10.94 10.94 0 0 1 19 12.55"/>
          <path d="M5 12.55a10.94 10.94 0 0 1 5.17-2.39"/>
          <path d="M10.71 5.05A16 16 0 0 1 22.58 9"/>
          <path d="M1.42 9a15.91 15.91 0 0 1 4.7-2.88"/>
          <path d="M8.53 16.11a6 6 0 0 1 6.95 0"/>
          <circle cx="12" cy="20" r="1"/>
        </svg>
      </div>

      <h1 class="offline-page__title">Você está <em>offline</em></h1>
      <p class="offline-page__text">
        Verifique sua conexão com a internet e tente novamente.
        Enquanto isso, você ainda pode navegar pelas páginas já visitadas.
      </p>

      <div class="offline-page__actions">
        <button type="button" class="btn btn-primary btn-lg" onclick="location.reload()">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <polyline points="23 4 23 10 17 10"/>
            <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
          </svg>
          Tentar novamente
        </button>
        <a href="<?= $base ?>/frontend/pages/index.php" class="btn btn-outline btn-lg">
          Voltar ao início
        </a>
      </div>
    </div>
  </div>
</main>
<?php require __DIR__ . '/../components/footer.php'; ?>