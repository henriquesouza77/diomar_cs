<?php
$pageTitle   = 'Recuperar senha';
$currentPage = 'recuperar';
require __DIR__ . '/../components/header.php';
$base = '/diomar-cs';
?>

<main class="auth-page">
  <div class="auth-visual">
    <div class="auth-visual__bg">
      <img src="<?= $base ?>/frontend/images/auth-bg.jpg" alt="" aria-hidden="true">
    </div>
    <div class="auth-visual__overlay"></div>
    <div class="auth-visual__content">
      <div class="auth-visual__logo">
        <span class="auth-visual__logo-icon">D</span>
        <div>
          <div class="auth-visual__logo-text">DIOMAR_CS</div>
          <div class="auth-visual__tagline">IMÓVEIS</div>
        </div>
      </div>
      <div>
        <h2 class="auth-visual__title">Recupere o acesso à sua <em>conta</em>.</h2>
        <p class="auth-visual__subtitle">Enviaremos um link seguro para redefinir sua senha.</p>
      </div>
    </div>
  </div>

  <div class="auth-form">
    <div class="auth-form__inner">
      <div class="auth-form__header">
        <h1 class="auth-form__title">Recuperar senha</h1>
        <p class="auth-form__subtitle">Informe o e-mail cadastrado</p>
      </div>

      <form class="auth-form__fields" id="recoverForm">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

        <div class="form-group">
          <label class="form-label" for="recEmail">E-mail</label>
          <div class="input-icon">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            <input type="email" id="recEmail" name="email" class="form-input" placeholder="seu@email.com" required>
          </div>
        </div>

        <button type="submit" class="btn btn-primary auth-form__submit">Enviar link de recuperação</button>
      </form>

      <div class="auth-form__footer">
        Lembrou a senha?
        <a href="<?= $base ?>/frontend/pages/login.php">Voltar ao login</a>
      </div>
    </div>
  </div>
</main>

<?php require __DIR__ . '/../components/footer.php'; ?>

<script>
  window.__PAGE__ = 'recuperar';
</script>