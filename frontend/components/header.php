<?php
/**
 * Cabeçalho global — inclui <head>, navbar e abertura do <body>
 */

// ============================================================
// BACKEND (config + helpers)
// ============================================================
$rootDir = dirname(__DIR__, 2);
require_once $rootDir . '/backend/config.php';
require_once $rootDir . '/backend/helpers.php';

$pageTitle       = $pageTitle       ?? 'DIOMAR_CS IMÓVEIS';
$pageDescription = $pageDescription ?? 'Encontre o imóvel dos seus sonhos.';
$currentPage     = $currentPage     ?? '';
$transparentNav  = $transparentNav  ?? false;
$bodyClass       = $bodyClass       ?? '';
$skipNavbar      = $skipNavbar      ?? false;

$base = '/diomar-cs';
// Item ativo do menu: listagem e detalhe de imóvel viram Comprar/Alugar conforme a finalidade
$navKey = $currentPage;
if ($navKey === 'imoveis') {
  $navKey = (($_GET['purpose'] ?? '') === 'aluguel') ? 'alugar' : 'comprar';
}


// ============================================================
// VERIFICAÇÃO DE USUÁRIO LOGADO
// ============================================================
$isLoggedIn     = !empty($_SESSION['user_id']);
$userRole       = $_SESSION['user_role'] ?? 'client';
$isAdminOrAgent = in_array($userRole, ['admin', 'agent'], true);
$userName       = $_SESSION['user_name'] ?? '';
$userInitial    = mb_strtoupper(mb_substr($userName ?: 'U', 0, 1));

$stylesheets = [
  '01-tokens.css', '02-reset.css', '03-typography.css', '04-layout.css',
  '05-buttons.css', '06-inputs.css', '07-cards.css', '08-badges.css',
  '09-navbar.css', '10-footer.css',
  '11-hero.css', '12-home-sections.css', '13-auth.css',
  '14-properties.css', '15-property-details.css',
  '16-admin.css',
  '17-animations.css', '18-utilities.css', '19-mobile.css',
  '20-polish.css', '21-motion.css', '22-dock-nav.css', '23-auth-glass.css',
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
  <meta name="theme-color" content="#0B1F4D">
  <meta name="color-scheme" content="light">
  <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

  <!-- PWA -->
  <link rel="manifest" href="<?= $base ?>/frontend/manifest.json">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="DIOMAR_CS">
  <link rel="apple-touch-icon" href="<?= $base ?>/frontend/assets/icons/icon-192.png">
  <link rel="apple-touch-icon" sizes="180x180" href="<?= $base ?>/frontend/assets/icons/icon-180.png">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="application-name" content="DIOMAR_CS">
  <meta name="msapplication-TileColor" content="#0B1F4D">

  <link rel="icon" type="image/svg+xml" href="<?= $base ?>/frontend/images/favicon.svg">

  <title><?= htmlspecialchars($pageTitle) ?> — DIOMAR_CS IMÓVEIS</title>

  <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?> — DIOMAR_CS IMÓVEIS">
  <meta property="og:description" content="<?= htmlspecialchars($pageDescription) ?>">
  <meta property="og:type" content="website">
  <meta property="og:locale" content="pt_BR">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,500;1,9..144,600&family=Inter:wght@400;500;600;700&family=Montserrat:wght@600;800&display=swap">

  <?php
  foreach ($stylesheets as $stylesheet):
    $stylesheetPath = __DIR__ . '/../css/' . $stylesheet;
    if (!file_exists($stylesheetPath)) continue;
  ?>
    <link rel="stylesheet" href="<?= $base ?>/frontend/css/<?= $stylesheet ?>?v=<?= filemtime($stylesheetPath) ?>">
  <?php endforeach; ?>
</head>
<body class="<?= htmlspecialchars(trim($bodyClass . ' has-bottom-nav')) ?>">

<?php if (!$skipNavbar): ?>

<!-- ==================== PAGE LOADER ==================== -->
<div class="page-loader" id="pageLoader" aria-hidden="true">
  <div class="page-loader__spinner"></div>
</div>

<!-- ==================== NAVBAR ==================== -->
<header class="navbar<?= $transparentNav ? ' is-transparent' : '' ?>" id="navbar" role="banner">
  <div class="container navbar__inner">

    <a href="<?= $base ?>/frontend/pages/index.php" class="navbar__logo" aria-label="DIOMAR_CS IMÓVEIS - Início">
      <span class="navbar__logo-icon" aria-hidden="true">D</span>
      <span class="navbar__logo-text">
        <span class="navbar__logo-name">DIOMAR_CS</span>
        <span class="navbar__logo-tagline">IMÓVEIS</span>
      </span>
    </a>

    <nav class="navbar__menu" role="navigation" aria-label="Menu principal">
      <div class="navbar__item">
        <a href="<?= $base ?>/frontend/pages/index.php"
           class="navbar__link<?= $navKey === 'home' ? ' is-active' : '' ?>"
           data-label="Início"<?= $navKey === 'home' ? ' aria-current="page"' : '' ?>>
          <svg class="navbar__link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1z"/><path d="M9.5 21v-6.5h5V21"/></svg>
          <span class="navbar__link-label">Início</span>
        </a>
      </div>

      <div class="navbar__item">
        <a href="<?= $base ?>/frontend/pages/imoveis.php?purpose=venda"
           class="navbar__link<?= $navKey === 'comprar' ? ' is-active' : '' ?>"
           data-label="Comprar"<?= $navKey === 'comprar' ? ' aria-current="page"' : '' ?>>
          <svg class="navbar__link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="3" width="14" height="18" rx="1.5"/><path d="M9 7h.01M15 7h.01M9 11h.01M15 11h.01"/><path d="M10 21v-4h4v4"/></svg>
          <span class="navbar__link-label">Comprar</span>
        </a>
      </div>

      <div class="navbar__item">
        <a href="<?= $base ?>/frontend/pages/imoveis.php?purpose=aluguel"
           class="navbar__link<?= $navKey === 'alugar' ? ' is-active' : '' ?>"
           data-label="Alugar"<?= $navKey === 'alugar' ? ' aria-current="page"' : '' ?>>
          <svg class="navbar__link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="7.5" cy="15.5" r="4.5"/><path d="m10.7 12.3 10-10M16 7l3 3M14 9l2 2"/></svg>
          <span class="navbar__link-label">Alugar</span>
        </a>
      </div>

      <div class="navbar__item">
        <a href="<?= $base ?>/frontend/pages/anunciar.php"
           class="navbar__link<?= $navKey === 'anunciar' ? ' is-active' : '' ?>"
           data-label="Anunciar"<?= $navKey === 'anunciar' ? ' aria-current="page"' : '' ?>>
          <svg class="navbar__link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M12 8v8M8 12h8"/></svg>
          <span class="navbar__link-label">Anunciar</span>
        </a>
      </div>

      <div class="navbar__item">
        <a href="<?= $base ?>/frontend/pages/sobre.php"
           class="navbar__link<?= $navKey === 'sobre' ? ' is-active' : '' ?>"
           data-label="Sobre"<?= $navKey === 'sobre' ? ' aria-current="page"' : '' ?>>
          <svg class="navbar__link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 8h.01"/></svg>
          <span class="navbar__link-label">Sobre</span>
        </a>
      </div>

      <div class="navbar__item">
        <a href="<?= $base ?>/frontend/pages/contato.php"
           class="navbar__link<?= $navKey === 'contato' ? ' is-active' : '' ?>"
           data-label="Contato"<?= $navKey === 'contato' ? ' aria-current="page"' : '' ?>>
          <svg class="navbar__link-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          <span class="navbar__link-label">Contato</span>
        </a>
      </div>
    </nav>

    <div class="navbar__actions">

      <!-- Favoritos (sempre visível) -->
      <a href="<?= $base ?>/frontend/pages/favoritos.php"
         class="navbar__icon-btn tooltip<?= $currentPage === 'favoritos' ? ' is-active' : '' ?>"
         data-tooltip="Favoritos"
         aria-label="Ver favoritos">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
        <span class="badge-count" id="favCount" hidden>0</span>
      </a>

      <?php if ($isLoggedIn): ?>

        <!-- ============ LOGADO ============ -->

        <?php if ($isAdminOrAgent): ?>
          <!-- Painel (admin/agent) — só desktop -->
          <a href="<?= $base ?>/frontend/pages/admin/dashboard.php"
             class="btn btn-primary btn-sm navbar__desktop-only">
            Painel
          </a>
        <?php else: ?>
          <!-- Minha conta (cliente) — só desktop -->
          <a href="<?= $base ?>/frontend/pages/favoritos.php"
             class="btn btn-primary btn-sm navbar__desktop-only">
            Minha conta
          </a>
        <?php endif; ?>

        <!-- Avatar com dropdown — só desktop -->
        <div class="navbar__user dropdown navbar__desktop-only">
          <button type="button"
                  class="navbar__user-btn"
                  data-dropdown-trigger
                  aria-label="Menu do usuário">
            <span class="navbar__user-avatar"><?= htmlspecialchars($userInitial) ?></span>
            <span class="navbar__user-name"><?= htmlspecialchars($userName) ?></span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="6 9 12 15 18 9"/>
            </svg>
          </button>
          <div class="dropdown__menu">
            <?php if ($isAdminOrAgent): ?>
              <a href="<?= $base ?>/frontend/pages/admin/dashboard.php" class="dropdown__item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>
                Painel
              </a>
            <?php endif; ?>
            <a href="<?= $base ?>/frontend/pages/favoritos.php" class="dropdown__item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
              Favoritos
            </a>
            <div class="dropdown__divider"></div>
            <a href="<?= $base ?>/frontend/pages/logout.php" class="dropdown__item is-danger">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
              Sair
            </a>
          </div>
        </div>

        <!-- Ícone de usuário MOBILE (logado) -->
        <a href="<?= $base ?>/frontend/pages/favoritos.php"
           class="navbar__icon-btn navbar__user-icon-mobile"
           aria-label="Minha conta">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
        </a>

      <?php else: ?>

        <!-- ============ VISITANTE ============ -->

        <!-- Botões desktop — escondidos em mobile -->
        <a href="<?= $base ?>/frontend/pages/cadastro.php"
           class="btn btn-ghost btn-sm navbar__desktop-only">
          Criar conta
        </a>
        <a href="<?= $base ?>/frontend/pages/login.php"
           class="btn btn-primary btn-sm navbar__desktop-only">
          Entrar
        </a>

        <!-- Ícone de usuário MOBILE (visitante) -->
        <a href="<?= $base ?>/frontend/pages/login.php"
           class="navbar__icon-btn navbar__user-icon-mobile"
           aria-label="Entrar ou criar conta">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
        </a>

      <?php endif; ?>

      <!-- Menu hamburger (mobile) -->
      <button class="navbar__toggle" id="navbarToggle" aria-label="Abrir menu" aria-expanded="false" aria-controls="navbarDrawer">
        <span class="navbar__toggle-bar" aria-hidden="true"></span>
      </button>
    </div>
  </div>

  <div class="navbar__overlay" id="navbarOverlay" aria-hidden="true"></div>
  <aside class="navbar__drawer" id="navbarDrawer" aria-label="Menu mobile">
    <nav class="navbar__drawer-menu">
      <a href="<?= $base ?>/frontend/pages/imoveis.php?purpose=venda" class="navbar__drawer-link">Comprar</a>
      <a href="<?= $base ?>/frontend/pages/imoveis.php?purpose=aluguel" class="navbar__drawer-link">Alugar</a>
      <a href="<?= $base ?>/frontend/pages/anunciar.php" class="navbar__drawer-link">Anunciar imóvel</a>
      <a href="<?= $base ?>/frontend/pages/sobre.php" class="navbar__drawer-link">Sobre</a>
      <a href="<?= $base ?>/frontend/pages/contato.php" class="navbar__drawer-link">Contato</a>
    </nav>

    <div class="navbar__drawer-actions">
      <?php if ($isLoggedIn): ?>
        <?php if ($isAdminOrAgent): ?>
          <a href="<?= $base ?>/frontend/pages/admin/dashboard.php" class="btn btn-primary btn-block">Painel</a>
        <?php endif; ?>
        <a href="<?= $base ?>/frontend/pages/favoritos.php" class="btn btn-outline btn-block">Meus favoritos</a>
        <a href="<?= $base ?>/frontend/pages/logout.php" class="btn btn-ghost btn-block">Sair</a>
      <?php else: ?>
        <a href="<?= $base ?>/frontend/pages/cadastro.php" class="btn btn-outline btn-block">Criar conta</a>
        <a href="<?= $base ?>/frontend/pages/login.php" class="btn btn-primary btn-block">Entrar</a>
      <?php endif; ?>
    </div>
  </aside>
</header>

<?php endif; ?>