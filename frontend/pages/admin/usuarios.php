<?php
$adminTitle  = 'Usuários';
$adminActive = 'usuarios';

require __DIR__ . '/_layout-top.php';
?>

<div class="admin-content">
  <div class="admin-page-header">
    <div>
      <h2 class="admin-page-header__title">Todos os usuários</h2>
      <p class="admin-page-header__subtitle">Administradores, corretores e clientes.</p>
    </div>
  </div>

  <div class="admin-toolbar">
    <div class="admin-toolbar__search">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="search" id="searchUsers" placeholder="Buscar por nome ou e-mail…">
    </div>
    <select class="admin-toolbar__select" id="filterRole">
      <option value="">Todos os perfis</option>
      <option value="1">Administrador</option>
      <option value="2">Corretor</option>
      <option value="3">Cliente</option>
    </select>
  </div>

  <div class="admin-panel">
    <div class="admin-panel__body admin-panel__body--flush">
      <div class="table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th></th>
              <th>Nome</th>
              <th>E-mail</th>
              <th>Telefone</th>
              <th>Perfil</th>
              <th>Status</th>
              <th style="text-align:right;">Ações</th>
            </tr>
          </thead>
          <tbody id="usersTable">
            <tr><td colspan="7" style="text-align:center;padding:var(--space-8);">Carregando…</td></tr>
          </tbody>
        </table>
      </div>
      <div id="usersPagination" class="admin-pagination" aria-label="Paginação de usuários"></div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/_layout-bottom.php'; ?>

<script>
  window.__ADMIN__ = 'usuarios';
</script>