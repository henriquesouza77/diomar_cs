<?php
$adminTitle  = 'Leads';
$adminActive = 'leads';

require __DIR__ . '/_layout-top.php';
?>

<div class="admin-content">
  <div class="admin-page-header">
    <div>
      <h2 class="admin-page-header__title">Leads recebidos</h2>
      <p class="admin-page-header__subtitle">Contatos e oportunidades geradas pelo site.</p>
    </div>
  </div>

  <div class="admin-toolbar">
    <div class="admin-toolbar__search">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="search" id="searchLeads" placeholder="Buscar por nome, e-mail ou imóvel…">
    </div>

    <select class="admin-toolbar__select" id="filterLeadStatus">
      <option value="">Todos os status</option>
      <option value="1">Novo</option>
      <option value="2">Em contato</option>
      <option value="3">Em negociação</option>
      <option value="4">Convertido</option>
      <option value="5">Perdido</option>
    </select>
  </div>

  <div class="admin-panel">
    <div class="admin-panel__body admin-panel__body--flush">
      <div class="table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Nome</th>
              <th>Contato</th>
              <th>Imóvel</th>
              <th>Status</th>
              <th>Data</th>
              <th style="text-align:right;">Ações</th>
            </tr>
          </thead>
          <tbody id="leadsTable">
            <tr><td colspan="6" style="text-align:center;padding:var(--space-8);">Carregando…</td></tr>
          </tbody>
        </table>
      </div>
      <div id="leadsPagination" class="admin-pagination" aria-label="Paginação de leads"></div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/_layout-bottom.php'; ?>

<script>
  window.__ADMIN__ = 'leads';
</script>