<?php
/**
 * Layout do topo do painel admin (sidebar + topbar).
 *
 * Variáveis esperadas:
 *   $adminTitle     - título da página
 *   $adminActive    - slug do item ativo no menu
 *   $adminSubtitle  - (opcional) subtítulo
 */
require_once __DIR__ . '/../../../backend/config/bootstrap.php';

$currentUser = \Middleware\AuthMiddleware::user();
if (!$currentUser) {
  \Core\Response::redirect('/diomar-cs/frontend/pages/login.php?redirect=' . rawurlencode($_SERVER['REQUEST_URI'] ?? '/diomar-cs/frontend/pages/admin/dashboard.php'));
}
if (!\Middleware\AuthMiddleware::isAgent()) {
  \Core\Response::redirect('/diomar-cs/frontend/pages/index.php');
}
if (in_array($adminActive ?? '', ['usuarios', 'corretores', 'configuracoes'], true)
    && !\Middleware\AuthMiddleware::isAdmin()) {
  \Core\Response::redirect('/diomar-cs/frontend/pages/admin/dashboard.php');
}

$base        = '/diomar-cs';
$adminTitle  = $adminTitle  ?? 'Dashboard';
$adminActive = $adminActive ?? 'dashboard';
$adminSubtitle = $adminSubtitle ?? '';
$userName    = $_SESSION['user_name'] ?? 'Administrador';
$userEmail   = $_SESSION['user_email'] ?? 'admin@diomar.com.br';
$userInitial = strtoupper(mb_substr($userName, 0, 1));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#0B1F4D">
  <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
  <title><?= htmlspecialchars($adminTitle) ?> — Painel DIOMAR_CS</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="icon" type="image/svg+xml" href="<?= $base ?>/frontend/images/favicon.svg">

  <?php
  $stylesheets = [
    '01-tokens.css',
    '02-reset.css',
    '03-typography.css',
    '04-layout.css',
    '05-buttons.css',
    '06-inputs.css',
    '07-cards.css',
    '08-badges.css',
    '16-admin.css',
    '17-animations.css',
    '18-utilities.css',
  ];
  foreach ($stylesheets as $stylesheet):
    $path = __DIR__ . '/../../css/' . $stylesheet;
    if (!file_exists($path)) continue;
  ?>
    <link rel="stylesheet" href="<?= $base ?>/frontend/css/<?= $stylesheet ?>?v=<?= filemtime($path) ?>">
  <?php endforeach; ?>
</head>
<body class="admin-body">

<div class="admin-layout">

  <!-- ==================== SIDEBAR ==================== -->
  <aside class="admin-sidebar" id="adminSidebar">

    <div class="admin-sidebar__logo">
      <span class="admin-sidebar__logo-icon">D</span>
      <span class="admin-sidebar__logo-text">
        <span class="admin-sidebar__logo-name">DIOMAR_CS</span>
        <span class="admin-sidebar__logo-tagline">ADMIN</span>
      </span>
    </div>

    <nav class="admin-nav" aria-label="Navegação do painel">

      <div class="admin-nav__label">Principal</div>

      <a href="<?= $base ?>/frontend/pages/admin/dashboard.php"
         class="admin-nav__item<?= $adminActive === 'dashboard' ? ' is-active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="3" width="7" height="9"/>
          <rect x="14" y="3" width="7" height="5"/>
          <rect x="14" y="12" width="7" height="9"/>
          <rect x="3" y="16" width="7" height="5"/>
        </svg>
        Dashboard
      </a>

      <a href="<?= $base ?>/frontend/pages/admin/imoveis.php"
         class="admin-nav__item<?= $adminActive === 'imoveis' ? ' is-active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
          <polyline points="9 22 9 12 15 12 15 22"/>
        </svg>
        Imóveis
      </a>

      <a href="<?= $base ?>/frontend/pages/admin/leads.php"
         class="admin-nav__item<?= $adminActive === 'leads' ? ' is-active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
          <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
          <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
        Leads
        <span class="admin-nav__badge" id="leadsBadge" hidden>0</span>
      </a>

      <?php if (\Middleware\AuthMiddleware::isAdmin()): ?>
        <a href="<?= $base ?>/frontend/pages/admin/corretores.php"
           class="admin-nav__item<?= $adminActive === 'corretores' ? ' is-active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
          Corretores
        </a>

        <a href="<?= $base ?>/frontend/pages/admin/usuarios.php"
           class="admin-nav__item<?= $adminActive === 'usuarios' ? ' is-active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="8.5" cy="7" r="4"/>
            <line x1="20" y1="8" x2="20" y2="14"/>
            <line x1="23" y1="11" x2="17" y2="11"/>
          </svg>
          Usuários
        </a>
      <?php endif; ?>

      <div class="admin-nav__label">Análise</div>

      <a href="<?= $base ?>/frontend/pages/admin/relatorios.php"
         class="admin-nav__item<?= $adminActive === 'relatorios' ? ' is-active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="20" x2="18" y2="10"/>
          <line x1="12" y1="20" x2="12" y2="4"/>
          <line x1="6" y1="20" x2="6" y2="14"/>
        </svg>
        Relatórios
      </a>

      <div class="admin-nav__label">Sistema</div>

      <?php if (\Middleware\AuthMiddleware::isAdmin()): ?>
        <a href="<?= $base ?>/frontend/pages/admin/configuracoes.php"
           class="admin-nav__item<?= $adminActive === 'configuracoes' ? ' is-active' : '' ?>">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="3"/>
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
          </svg>
          Configurações
        </a>
      <?php endif; ?>

      <a href="<?= $base ?>/frontend/pages/index.php" target="_blank" class="admin-nav__item">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
          <polyline points="15 3 21 3 21 9"/>
          <line x1="10" y1="14" x2="21" y2="3"/>
        </svg>
        Ver site
      </a>
    </nav>

    <div class="admin-sidebar__footer">
      <button type="button" class="admin-nav__item" data-admin-logout>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
          <polyline points="16 17 21 12 16 7"/>
          <line x1="21" y1="12" x2="9" y2="12"/>
        </svg>
        Sair
      </button>
    </div>
  </aside>

  <div class="admin-sidebar-overlay" id="adminSidebarOverlay"></div>

  <!-- ==================== MAIN ==================== -->
  <main class="admin-main">

    <header class="admin-topbar">
      <div class="admin-topbar__left">
        <button type="button" class="admin-sidebar-toggle" id="adminSidebarToggle"
                aria-label="Abrir menu" aria-controls="adminSidebar" aria-expanded="false">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
            <line x1="3" y1="6" x2="21" y2="6"/>
            <line x1="3" y1="12" x2="21" y2="12"/>
            <line x1="3" y1="18" x2="21" y2="18"/>
          </svg>
        </button>
        <h1 class="admin-topbar__title"><?= htmlspecialchars($adminTitle) ?></h1>
      </div>

      <div class="admin-topbar__right">
        <div class="admin-search">
          <svg class="admin-search__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"/>
            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
          <input type="search" class="admin-search__input" placeholder="Buscar..." aria-label="Buscar">
        </div>

        <button class="admin-notification-btn" aria-label="Notificações">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
          </svg>
          <span class="admin-notification-dot"></span>
        </button>

        <div class="admin-user">
          <div class="admin-user__avatar"><?= htmlspecialchars($userInitial) ?></div>
          <span class="admin-user__name"><?= htmlspecialchars($userName) ?></span>
        </div>
      </div>
    </header>