/**
 * auth.js — Login e Cadastro
 * Gerencia label flutuante, mostrar senha, força de senha, submit AJAX.
 */
'use strict';

(() => {
  const page = window.__PAGE__;
  if (page !== 'login' && page !== 'cadastro') return;

  // ========================================================================
  // LABEL FLUTUANTE
  // ========================================================================
  document.querySelectorAll('[data-field]').forEach(field => {
    const input = field.querySelector('.field__input, .field__textarea, .field__select');
    if (!input) return;

    const updateState = () => {
      field.classList.toggle('field--filled', input.value.trim() !== '');
    };

    input.addEventListener('focus', () => field.classList.add('field--focus'));
    input.addEventListener('blur', () => {
      field.classList.remove('field--focus');
      updateState();
    });
    input.addEventListener('input', () => {
      updateState();
      field.classList.remove('field--error');
    });

    updateState();
  });

  // ========================================================================
  // MOSTRAR / ESCONDER SENHA
  // ========================================================================
  document.querySelectorAll('[data-toggle-password]').forEach(btn => {
    btn.addEventListener('click', () => {
      const field = btn.closest('.field');
      const input = field.querySelector('input');
      if (!input) return;

      const isPassword = input.type === 'password';
      input.type = isPassword ? 'text' : 'password';

      // Troca ícone
      btn.innerHTML = isPassword
        ? `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
             <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
             <line x1="1" y1="1" x2="23" y2="23"/>
           </svg>`
        : `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
             <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
             <circle cx="12" cy="12" r="3"/>
           </svg>`;

      btn.setAttribute('aria-label', isPassword ? 'Esconder senha' : 'Mostrar senha');
    });
  });

  // ========================================================================
  // LOGIN COM GOOGLE
  // ========================================================================
    document.querySelectorAll('[data-google-login]').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        // Usa a rota amigável reescrita pelo .htaccess raiz
        window.location.href = `${API.BASE_URL}/api/auth/google`;
      });
    });

  // ========================================================================
  // FORÇA DA SENHA
  // ========================================================================
  const $password = document.getElementById('regPassword');
  const $strength = document.getElementById('pwdStrength');
  const $strengthLabel = document.getElementById('pwdStrengthLabel');

  if ($password && $strength && $strengthLabel) {
    $password.addEventListener('input', () => {
      const val = $password.value;
      let score = 0;
      if (val.length >= 8) score++;
      if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score++;
      if (/\d/.test(val) && /[^A-Za-z0-9]/.test(val)) score++;

      const level = score <= 1 ? 'weak' : score === 2 ? 'medium' : 'strong';
      $strength.dataset.strength = level;

      const labels = {
        weak: 'Senha fraca — adicione mais caracteres',
        medium: 'Senha média — adicione símbolos',
        strong: 'Senha forte',
      };
      $strengthLabel.textContent = val ? labels[level] : 'Força da senha';
    });
  }

  // ========================================================================
  // LOGIN
  // ========================================================================
  const $loginForm = document.getElementById('loginForm');
  $loginForm?.addEventListener('submit', async (e) => {
    e.preventDefault();

    const btn = $loginForm.querySelector('button[type="submit"]');
    const original = btn.innerHTML;

    btn.disabled = true;
    btn.classList.add('is-loading');
    btn.innerHTML = '';

    try {
      const data = Object.fromEntries(new FormData($loginForm));
      const res = await API.auth.login(data);

      App.toast(`Bem-vindo, ${res.user?.name || ''}!`, 'success', 1500);

      const requestedRedirect = new URLSearchParams(location.search).get('redirect');
      const redirectUrl = requestedRedirect
        ? new URL(requestedRedirect, location.origin)
        : null;
      const redirect = redirectUrl
        && redirectUrl.origin === location.origin
        && requestedRedirect.startsWith('/')
        && !requestedRedirect.startsWith('//')
          ? redirectUrl.href
          : null;
      const defaultRedirect = ['admin', 'agent'].includes(res.user?.role)
        ? `${API.BASE_URL}/frontend/pages/admin/dashboard.php`
        : `${API.BASE_URL}/frontend/pages/index.php`;

      setTimeout(() => {
        location.href = redirect || defaultRedirect;
      }, 700);
    } catch (err) {
      App.toast(err.message || 'E-mail ou senha incorretos.', 'error');

      // Aplica classe de erro ao formulário
      $loginForm.querySelectorAll('.field').forEach(f => {
        f.classList.add('field--error');
        setTimeout(() => f.classList.remove('field--error'), 3000);
      });

      btn.disabled = false;
      btn.classList.remove('is-loading');
      btn.innerHTML = original;
    }
  });

  // ========================================================================
  // CADASTRO
  // ========================================================================
  const $registerForm = document.getElementById('registerForm');
  $registerForm?.addEventListener('submit', async (e) => {
    e.preventDefault();

    const data = Object.fromEntries(new FormData($registerForm));

    // Validações
    if (data.password.length < 8) {
      App.toast('A senha deve ter pelo menos 8 caracteres.', 'warning');
      return;
    }
    if (data.password !== data.password_confirm) {
      App.toast('As senhas não coincidem.', 'warning');
      return;
    }
    if (!data.phone?.trim()) {
      App.toast('Informe seu telefone ou WhatsApp.', 'warning');
      return;
    }
    if (!data.terms) {
      App.toast('É necessário aceitar os termos.', 'warning');
      return;
    }

    const btn = $registerForm.querySelector('button[type="submit"]');
    const original = btn.innerHTML;

    btn.disabled = true;
    btn.classList.add('is-loading');
    btn.innerHTML = '';

    try {
      await API.auth.register(data);
      App.toast('Conta criada com sucesso!', 'success', 1500);
      setTimeout(() => location.href = `${API.BASE_URL}/frontend/pages/login.php`, 900);
    } catch (err) {
      const fieldErrors = err.data?.fields || {};
      Object.entries(fieldErrors).forEach(([name]) => {
        const control = $registerForm.elements.namedItem(name);
        control?.closest('.field')?.classList.add('field--error');
      });
      App.toast(Object.values(fieldErrors)[0] || err.message || 'Erro ao criar conta.', 'error');
      btn.disabled = false;
      btn.classList.remove('is-loading');
      btn.innerHTML = original;
    }
  });
})();