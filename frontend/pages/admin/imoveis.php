<?php
$adminTitle  = 'Imóveis';
$adminActive = 'imoveis';

require __DIR__ . '/_layout-top.php';
?>

<div class="admin-content">

  <div class="admin-page-header">
    <div>
      <h2 class="admin-page-header__title">Gerenciar imóveis</h2>
      <p class="admin-page-header__subtitle">Cadastre, edite e publique imóveis.</p>
    </div>
    <div class="admin-page-header__actions">
      <a href="<?= $base ?>/frontend/pages/admin/imovel-form.php" class="btn btn-primary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Adicionar imóvel
      </a>
    </div>
  </div>

  <div class="admin-toolbar">
    <div class="admin-toolbar__search">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="search" id="searchProperties" placeholder="Buscar por título, código ou cidade…">
    </div>

    <select class="admin-toolbar__select" id="filterStatus">
      <option value="">Todos os status</option>
      <option value="disponivel">Disponível</option>
      <option value="vendido">Vendido</option>
      <option value="alugado">Alugado</option>
      <option value="reservado">Reservado</option>
      <option value="inativo">Inativo</option>
    </select>

    <select class="admin-toolbar__select" id="filterPurpose">
      <option value="">Venda e aluguel</option>
      <option value="venda">Venda</option>
      <option value="aluguel">Aluguel</option>
    </select>
  </div>

  <div class="admin-panel">
    <div class="admin-panel__body admin-panel__body--flush">
      <div class="table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th></th>
              <th>Título</th>
              <th>Endereço</th>
              <th>Preço</th>
              <th>Status</th>
              <th>Publicação / destaque</th>
              <th style="text-align:right;">Ações</th>
            </tr>
          </thead>
          <tbody id="propertiesTable">
            <tr><td colspan="7" style="text-align:center;padding:var(--space-8);">Carregando…</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div id="propertiesPagination" aria-label="Paginação de imóveis"></div>
</div>

<?php require __DIR__ . '/_layout-bottom.php'; ?>

<script>
  window.__ADMIN__ = 'imoveis';
</script>