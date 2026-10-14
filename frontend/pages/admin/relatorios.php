<?php
$adminTitle  = 'Relatórios';
$adminActive = 'relatorios';

require __DIR__ . '/_layout-top.php';
?>

<div class="admin-content">
  <div class="admin-page-header">
    <div>
      <h2 class="admin-page-header__title">Relatórios</h2>
      <p class="admin-page-header__subtitle">Análise de desempenho da imobiliária.</p>
    </div>
  </div>

  <div class="admin-panel">
    <div class="admin-panel__header">
      <h3 class="admin-panel__title">Imóveis por status</h3>
    </div>
    <div class="admin-panel__body">
      <div class="chart-container" id="chartPropertiesByStatus"></div>
    </div>
  </div>

  <div class="admin-panel">
    <div class="admin-panel__header">
      <h3 class="admin-panel__title">Leads por mês</h3>
    </div>
    <div class="admin-panel__body">
      <div class="chart-container" id="chartLeadsByMonth"></div>
    </div>
  </div>

  <div class="admin-panel">
    <div class="admin-panel__header">
      <h3 class="admin-panel__title">Imóveis por tipo</h3>
    </div>
    <div class="admin-panel__body">
      <div class="chart-container" id="chartPropertiesByType"></div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/_layout-bottom.php'; ?>

<script>
  window.__ADMIN__ = 'relatorios';
</script>