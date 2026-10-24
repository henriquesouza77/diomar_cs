<?php
$adminTitle  = 'Configurações';
$adminActive = 'configuracoes';

require __DIR__ . '/_layout-top.php';
?>

<div class="admin-content">
  <div class="admin-page-header">
    <div>
      <h2 class="admin-page-header__title">Configurações do site</h2>
      <p class="admin-page-header__subtitle">Informações exibidas no site público.</p>
    </div>
  </div>

  <form class="property-form" id="settingsForm">
    <section class="form-section">
      <div class="form-section__header">
        <span class="form-section__number">1</span>
        <h3 class="form-section__title">Identidade</h3>
      </div>

      <div class="form-grid form-grid--2">
        <div class="form-group">
          <label class="form-label">Nome do site</label>
          <input type="text" name="site_name" class="form-input" value="DIOMAR_CS IMÓVEIS">
        </div>
        <div class="form-group">
          <label class="form-label">E-mail de contato</label>
          <input type="email" name="site_email" class="form-input" value="contato@diomarcsimoveis.com.br">
        </div>
        <div class="form-group">
          <label class="form-label">Telefone</label>
          <input type="text" name="site_phone" class="form-input" value="(11) 99999-9999">
        </div>
        <div class="form-group">
          <label class="form-label">WhatsApp</label>
          <input type="text" name="site_whatsapp" class="form-input" value="5511939565958">
        </div>
        <div class="form-group form-grid__full">
          <label class="form-label">Endereço</label>
          <input type="text" name="site_address" class="form-input" value="São Paulo - SP">
        </div>
      </div>
    </section>

    <div style="display:flex;justify-content:flex-end;">
      <button type="submit" class="btn btn-primary btn-lg">Salvar configurações</button>
    </div>
  </form>
</div>

<?php require __DIR__ . '/_layout-bottom.php'; ?>

<script>
  window.__ADMIN__ = 'configuracoes';
</script>