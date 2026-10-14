<?php
$adminTitle  = 'Corretores';
$adminActive = 'corretores';

require __DIR__ . '/_layout-top.php';
?>

<div class="admin-content">
  <div class="admin-page-header">
    <div>
      <h2 class="admin-page-header__title">Corretores</h2>
      <p class="admin-page-header__subtitle">Gerencie a equipe de corretores.</p>
    </div>
    <div class="admin-page-header__actions">
      <button class="btn btn-primary" id="btnAddAgent">＋ Adicionar corretor</button>
    </div>
  </div>

  <dialog class="admin-dialog" id="agentDialog">
    <form id="agentForm" class="property-form">
      <h2 class="admin-panel__title">Adicionar corretor</h2>
      <div class="form-group">
        <label class="form-label" for="agentName">Nome</label>
        <input class="form-input" id="agentName" name="name" required>
      </div>
      <div class="form-group">
        <label class="form-label" for="agentEmail">E-mail</label>
        <input class="form-input" id="agentEmail" name="email" type="email" required>
      </div>
      <div class="form-group">
        <label class="form-label" for="agentPhone">Telefone</label>
        <input class="form-input" id="agentPhone" name="phone" type="tel">
      </div>
      <div class="form-group">
        <label class="form-label" for="agentCreci">CRECI</label>
        <input class="form-input" id="agentCreci" name="creci">
      </div>
      <div class="form-group">
        <label class="form-label" for="agentPassword">Senha inicial</label>
        <input class="form-input" id="agentPassword" name="password" type="password" minlength="8" required>
      </div>
      <div class="admin-page-header__actions">
        <button class="btn btn-ghost" type="button" data-close-agent-dialog>Cancelar</button>
        <button class="btn btn-primary" type="submit">Criar corretor</button>
      </div>
    </form>
  </dialog>

  <div class="admin-panel">
    <div class="admin-panel__body admin-panel__body--flush">
      <div class="table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th></th>
              <th>Nome</th>
              <th>E-mail</th>
              <th>CRECI</th>
              <th>Imóveis</th>
              <th>Status</th>
              <th style="text-align:right;">Ações</th>
            </tr>
          </thead>
          <tbody id="agentsTable">
            <tr><td colspan="7" style="text-align:center;padding:var(--space-8);">Carregando…</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/_layout-bottom.php'; ?>

<script>
  window.__ADMIN__ = 'corretores';
</script>