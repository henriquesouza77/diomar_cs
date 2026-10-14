<?php
$adminTitle    = 'Dashboard';
$adminActive   = 'dashboard';
$adminSubtitle = 'Visão geral da operação';

require __DIR__ . '/_layout-top.php';
?>

<div class="admin-content">

  <header class="admin-page-header">
    <div>
      <h2 class="admin-page-header__title">Olá, <?= htmlspecialchars($userName) ?></h2>
      <p class="admin-page-header__subtitle">Aqui está o resumo da sua operação hoje.</p>
    </div>
    <div class="admin-page-header__actions">
      <a href="<?= $base ?>/frontend/pages/admin/imovel-form.php" class="btn btn-primary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
          <line x1="12" y1="5" x2="12" y2="19"/>
          <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Adicionar imóvel
      </a>
    </div>
  </header>

  <!-- ==================== KPIs ==================== -->
  <section class="kpi-grid">

    <article class="kpi-card">
      <div class="kpi-card__header">
        <span class="kpi-card__label">Total de imóveis</span>
        <span class="kpi-card__icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
            <polyline points="9 22 9 12 15 12 15 22"/>
          </svg>
        </span>
      </div>
      <div class="kpi-card__value">
        <span class="kpi-card__value-main" data-stat="total_properties">—</span>
      </div>
      <span class="kpi-card__delta kpi-card__delta--up">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round">
          <polyline points="18 15 12 9 6 15"/>
        </svg>
        <span data-delta="total_properties">+12%</span>
      </span>
    </article>

    <article class="kpi-card kpi-card--success">
      <div class="kpi-card__header">
        <span class="kpi-card__label">Disponíveis</span>
        <span class="kpi-card__icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
        </span>
      </div>
      <div class="kpi-card__value">
        <span class="kpi-card__value-main" data-stat="available">—</span>
      </div>
      <span class="kpi-card__delta kpi-card__delta--up">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round">
          <polyline points="18 15 12 9 6 15"/>
        </svg>
        <span data-delta="available">+4%</span>
      </span>
    </article>

    <article class="kpi-card kpi-card--gold">
      <div class="kpi-card__header">
        <span class="kpi-card__label">Leads hoje</span>
        <span class="kpi-card__icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
          </svg>
        </span>
      </div>
      <div class="kpi-card__value">
        <span class="kpi-card__value-main" data-stat="leads_today">—</span>
      </div>
      <span class="kpi-card__delta kpi-card__delta--up">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round">
          <polyline points="18 15 12 9 6 15"/>
        </svg>
        <span data-delta="leads_today">+22%</span>
      </span>
    </article>

    <article class="kpi-card">
      <div class="kpi-card__header">
        <span class="kpi-card__label">Corretores ativos</span>
        <span class="kpi-card__icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
        </span>
      </div>
      <div class="kpi-card__value">
        <span class="kpi-card__value-main" data-stat="active_agents">—</span>
      </div>
      <span class="kpi-card__delta kpi-card__delta--flat">
        <span data-delta="active_agents">sem alteração</span>
      </span>
    </article>
  </section>

  <!-- ==================== AÇÕES RÁPIDAS ==================== -->
  <section class="admin-panel">
    <header class="admin-panel__header">
      <h3 class="admin-panel__title">Ações rápidas</h3>
    </header>
    <div class="admin-panel__body">
      <div class="quick-actions">

        <a href="<?= $base ?>/frontend/pages/admin/imovel-form.php" class="quick-action quick-action--gold">
          <span class="quick-action__icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
              <line x1="12" y1="5" x2="12" y2="19"/>
              <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
          </span>
          Adicionar imóvel
        </a>

        <a href="<?= $base ?>/frontend/pages/admin/leads.php" class="quick-action">
          <span class="quick-action__icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
              <circle cx="9" cy="7" r="4"/>
            </svg>
          </span>
          Ver leads
        </a>

        <?php if (\Middleware\AuthMiddleware::isAdmin()): ?>
        <a href="<?= $base ?>/frontend/pages/admin/corretores.php" class="quick-action">
          <span class="quick-action__icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
          </span>
          Corretores
        </a>
        <?php endif; ?>

        <a href="<?= $base ?>/frontend/pages/admin/relatorios.php" class="quick-action">
          <span class="quick-action__icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
              <line x1="18" y1="20" x2="18" y2="10"/>
              <line x1="12" y1="20" x2="12" y2="4"/>
              <line x1="6" y1="20" x2="6" y2="14"/>
            </svg>
          </span>
          Ver relatórios
        </a>
      </div>
    </div>
  </section>

  <!-- ==================== IMÓVEIS RECENTES ==================== -->
  <section class="admin-panel">
    <header class="admin-panel__header">
      <div>
        <h3 class="admin-panel__title">Imóveis recentes</h3>
        <p class="admin-panel__subtitle">Últimos cadastros no sistema</p>
      </div>
      <div class="admin-panel__actions">
        <a href="<?= $base ?>/frontend/pages/admin/imoveis.php" class="btn btn-ghost btn-sm">Ver todos</a>
      </div>
    </header>
    <div class="admin-panel__body admin-panel__body--flush">
      <div class="admin-table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th></th>
              <th>Imóvel</th>
              <th>Localização</th>
              <th>Preço</th>
              <th>Status</th>
              <th></th>
            </tr>
          </thead>
          <tbody id="recentProperties">
            <tr>
              <td colspan="6" style="text-align: center; padding: 40px;">
                <div class="property-loading" style="padding: 0;">
                  <div class="spinner"></div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

<?php require __DIR__ . '/_layout-bottom.php'; ?>

<script>
  window.__ADMIN__ = 'dashboard';
</script>