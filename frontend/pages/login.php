<?php
$pageTitle       = 'Entrar';
$pageDescription = 'Acesse sua conta DIOMAR_CS IMÓVEIS.';
$currentPage     = 'login';
$skipNavbar      = true;

require_once __DIR__ . '/../components/header.php';

$base = BASE_PATH;
?>

<?php require __DIR__ . '/../components/splash.php'; ?>

<main class="auth">

  <!-- ==================== LADO VISUAL ==================== -->
  <aside class="auth__visual" aria-hidden="true">

    <!-- Fundo com imagem -->
    <div class="auth__visual-media">
      <img src="<?= $base ?>/frontend/images/auth-bg.svg"
           alt=""
           onerror="this.src='<?= $base ?>/frontend/images/placeholder.svg'">
    </div>

    <div class="auth__visual-overlay"></div>
    <div class="auth__visual-grid"></div>
    <div class="auth__visual-blob auth__visual-blob--1"></div>
    <div class="auth__visual-blob auth__visual-blob--2"></div>

    <div class="auth__visual-content">

      <!-- Logo -->
      <a href="<?= $base ?>/frontend/pages/index.php" class="auth__visual-logo">
        <span class="auth__visual-logo-icon">D</span>
        <span class="auth__visual-logo-text">
          <span class="auth__visual-logo-name">DIOMAR_CS</span>
          <span class="auth__visual-logo-tagline">IMÓVEIS</span>
        </span>
      </a>

      <!-- Conteúdo central -->
      <div class="auth__visual-body">
        <span class="auth__visual-eyebrow">Área exclusiva</span>

        <h1 class="auth__visual-title">
          Onde imóveis<br>
          encontram <em>histórias</em>.
        </h1>

        <p class="auth__visual-subtitle">
          Acesse sua área exclusiva e continue a busca pelo imóvel
          que vai se tornar o cenário da sua próxima história.
        </p>

        <ul class="auth__visual-features">
          <li class="auth__visual-feature">
            <svg class="auth__visual-feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
            Favoritos sincronizados em todos os dispositivos
          </li>
          <li class="auth__visual-feature">
            <svg class="auth__visual-feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
            Alertas personalizados para novos imóveis
          </li>
          <li class="auth__visual-feature">
            <svg class="auth__visual-feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
            Atendimento prioritário com consultor dedicado
          </li>
        </ul>
      </div>

      <!-- Rodapé -->
      <footer class="auth__visual-footer">
        <span class="auth__visual-footer-text">São Paulo · Brasil</span>

        <div class="auth__visual-footer-stats">
          <div class="auth__visual-stat">
            <span class="auth__visual-stat-value">15<small>+</small></span>
            <span class="auth__visual-stat-label">Anos</span>
          </div>
          <div class="auth__visual-stat">
            <span class="auth__visual-stat-value">1200<small>+</small></span>
            <span class="auth__visual-stat-label">Imóveis</span>
          </div>
        </div>
      </footer>
    </div>
  </aside>

  <!-- ==================== FORMULÁRIO ==================== -->
  <section class="auth__form">

    <div class="auth__form-inner">

      <!-- Logo mobile -->
      <div class="auth__form-logo">
        <span class="auth__form-logo-icon">D</span>
        <span class="auth__form-logo-name">DIOMAR_CS</span>
      </div>

      <!-- Header -->
      <header class="auth__form-header">
        <h2 class="auth__form-title">Bem-vindo <em>de volta</em></h2>
        <p class="auth__form-subtitle">Acesse sua conta para continuar.</p>
      </header>

      <!-- Social -->
      <div class="auth__social">
        <button type="button" class="auth__social-btn" data-google-login>
          <svg viewBox="0 0 48 48" aria-hidden="true">
            <path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303c-1.649 4.657-6.08 8-11.303 8-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/>
            <path fill="#FF3D00" d="M6.306 14.691l6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z"/>
            <path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238C29.211 35.091 26.715 36 24 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z"/>
            <path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303c-.792 2.237-2.231 4.166-4.087 5.571.001-.001.002-.001.003-.002l6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z"/>
          </svg>
          Continuar com Google
        </button>
      </div>

      <div class="auth__divider">ou</div>

      <!-- Form -->
      <form class="auth__fields" id="loginForm" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

        <div class="field field--icon" data-field>
          <svg class="field__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
            <polyline points="22,6 12,13 2,6"/>
          </svg>
          <input type="email" id="loginEmail" name="email" class="field__input" placeholder=" " required autocomplete="email">
          <label for="loginEmail" class="field__label">E-mail</label>
        </div>

        <div class="field field--icon" data-field>
          <svg class="field__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="11" width="18" height="11" rx="2"/>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
          </svg>
          <input type="password" id="loginPassword" name="password" class="field__input" placeholder=" " required autocomplete="current-password">
          <label for="loginPassword" class="field__label">Senha</label>
          <button type="button" class="field__icon-right" data-toggle-password aria-label="Mostrar senha">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
              <circle cx="12" cy="12" r="3"/>
            </svg>
          </button>
        </div>

        <div class="auth__row auth__row--end">
          <a href="<?= $base ?>/frontend/pages/recuperar-senha.php" class="auth__link">
            Esqueceu a senha?
          </a>
        </div>

        <button type="submit" class="btn btn-primary btn-lg btn-block auth__submit">
          Entrar
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <line x1="5" y1="12" x2="19" y2="12"/>
            <polyline points="12 5 19 12 12 19"/>
          </svg>
        </button>
      </form>

      <!-- Footer -->
      <div class="auth__footer">
        Não possui conta?
        <a href="<?= $base ?>/frontend/pages/cadastro.php">Cadastre-se</a>
      </div>
    </div>
  </section>
</main>

<?php require __DIR__ . '/../components/footer.php'; ?>

<script>
  window.__PAGE__ = 'login';
</script>