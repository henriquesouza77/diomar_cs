<?php
$pageTitle       = 'Criar conta';
$pageDescription = 'Crie sua conta na DIOMAR_CS IMÓVEIS.';
$currentPage     = 'cadastro';
$skipNavbar      = true;

require_once __DIR__ . '/../components/header.php';

$base = '/diomar-cs';
?>

<main class="auth">

  <!-- ==================== LADO VISUAL ==================== -->
  <aside class="auth__visual" aria-hidden="true">

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

      <a href="<?= $base ?>/frontend/pages/index.php" class="auth__visual-logo">
        <span class="auth__visual-logo-icon">D</span>
        <span class="auth__visual-logo-text">
          <span class="auth__visual-logo-name">DIOMAR_CS</span>
          <span class="auth__visual-logo-tagline">IMÓVEIS</span>
        </span>
      </a>

      <div class="auth__visual-body">
        <span class="auth__visual-eyebrow">Comece agora</span>

        <h1 class="auth__visual-title">
          Sua jornada<br>
          começa <em>aqui</em>.
        </h1>

        <p class="auth__visual-subtitle">
          Crie sua conta gratuitamente e tenha acesso a uma
          curadoria exclusiva de imóveis de alto padrão.
        </p>

        <ul class="auth__visual-features">
          <li class="auth__visual-feature">
            <svg class="auth__visual-feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
            Salve imóveis favoritos e organize sua busca
          </li>
          <li class="auth__visual-feature">
            <svg class="auth__visual-feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
            Receba alertas de novos imóveis do seu perfil
          </li>
          <li class="auth__visual-feature">
            <svg class="auth__visual-feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="20 6 9 17 4 12"/>
            </svg>
            Anuncie seus imóveis para nossa base qualificada
          </li>
        </ul>
      </div>

      <footer class="auth__visual-footer">
        <span class="auth__visual-footer-text">Cadastro gratuito</span>

        <div class="auth__visual-footer-stats">
          <div class="auth__visual-stat">
            <span class="auth__visual-stat-value">98<small>%</small></span>
            <span class="auth__visual-stat-label">Satisfação</span>
          </div>
          <div class="auth__visual-stat">
            <span class="auth__visual-stat-value">24<small>h</small></span>
            <span class="auth__visual-stat-label">Resposta</span>
          </div>
        </div>
      </footer>
    </div>
  </aside>

  <!-- ==================== FORMULÁRIO ==================== -->
  <section class="auth__form">

    <div class="auth__form-inner">

      <div class="auth__form-logo">
        <span class="auth__form-logo-icon">D</span>
        <span class="auth__form-logo-name">DIOMAR_CS</span>
      </div>

      <header class="auth__form-header">
        <h2 class="auth__form-title">Criar sua <em>conta</em></h2>
        <p class="auth__form-subtitle">É rápido, gratuito e sem compromisso.</p>
      </header>

      <div class="auth__social">
        <button type="button" class="auth__social-btn" disabled>
          <svg viewBox="0 0 48 48" aria-hidden="true">
            <path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303c-1.649 4.657-6.08 8-11.303 8-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/>
            <path fill="#FF3D00" d="M6.306 14.691l6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z"/>
            <path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238C29.211 35.091 26.715 36 24 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z"/>
            <path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303c-.792 2.237-2.231 4.166-4.087 5.571.001-.001.002-.001.003-.002l6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z"/>
          </svg>
          Cadastrar com Google
        </button>
      </div>

      <div class="auth__divider">ou</div>

      <form class="auth__fields" id="registerForm" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

        <div class="field field--icon" data-field>
          <svg class="field__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
          <input type="text" id="regName" name="name" class="field__input" placeholder=" " required autocomplete="name">
          <label for="regName" class="field__label">Nome completo</label>
        </div>

        <div class="field field--icon" data-field>
          <svg class="field__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
            <polyline points="22,6 12,13 2,6"/>
          </svg>
          <input type="email" id="regEmail" name="email" class="field__input" placeholder=" " required autocomplete="email">
          <label for="regEmail" class="field__label">E-mail</label>
        </div>

        <div class="field field--icon" data-field>
          <svg class="field__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
          </svg>
          <input type="tel" id="regPhone" name="phone" class="field__input" placeholder=" " required autocomplete="tel">
          <label for="regPhone" class="field__label">Telefone / WhatsApp</label>
        </div>

        <div class="field field--icon" data-field>
          <svg class="field__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="11" width="18" height="11" rx="2"/>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
          </svg>
          <input type="password" id="regPassword" name="password" class="field__input" placeholder=" " required minlength="8" autocomplete="new-password">
          <label for="regPassword" class="field__label">Senha</label>
          <button type="button" class="field__icon-right" data-toggle-password aria-label="Mostrar senha">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
              <circle cx="12" cy="12" r="3"/>
            </svg>
          </button>
        </div>

        <div class="auth__strength" id="pwdStrength" data-strength="weak">
          <span class="auth__strength-bar"></span>
          <span class="auth__strength-bar"></span>
          <span class="auth__strength-bar"></span>
        </div>
        <span class="auth__strength-label" id="pwdStrengthLabel">Força da senha</span>

        <div class="field field--icon" data-field>
          <svg class="field__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="11" width="18" height="11" rx="2"/>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
          </svg>
          <input type="password" id="regPasswordConfirm" name="password_confirm" class="field__input" placeholder=" " required autocomplete="new-password">
          <label for="regPasswordConfirm" class="field__label">Confirmar senha</label>
        </div>

        <label class="auth__terms">
          <input type="checkbox" name="terms" required>
          <span>
            Concordo com os
            <a href="<?= $base ?>/frontend/pages/termos.php" target="_blank">Termos de uso</a>
            e a
            <a href="<?= $base ?>/frontend/pages/privacidade.php" target="_blank">Política de privacidade</a>.
          </span>
        </label>

        <button type="submit" class="btn btn-primary btn-lg btn-block auth__submit">
          Criar minha conta
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <line x1="5" y1="12" x2="19" y2="12"/>
            <polyline points="12 5 19 12 12 19"/>
          </svg>
        </button>
      </form>

      <div class="auth__footer">
        Já possui conta?
        <a href="<?= $base ?>/frontend/pages/login.php">Entrar</a>
      </div>
    </div>
  </section>
</main>

<?php require __DIR__ . '/../components/footer.php'; ?>

<script>
  window.__PAGE__ = 'cadastro';
</script>