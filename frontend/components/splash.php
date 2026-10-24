<?php
/**
 * Abertura animada (logo + zoom na casinha). Usada nas telas de login e cadastro.
 * Lógica e tempos em js/splash.js; estilos em css/23-auth-glass.css.
 */
$base = $base ?? '/diomar-cs';
?>
<!-- ==================== ABERTURA ANIMADA ==================== -->
<div class="splash" id="splash" role="dialog" aria-label="Abertura DIOMAR_CS IMÓVEIS">
  <div class="splash__letters" data-text="diomar" aria-hidden="true"></div>
  <div class="splash__tagline" aria-hidden="true">IMÓVEIS</div>
  <div class="splash__symbol" aria-hidden="true">
    <svg viewBox="0 0 100 100">
      <path fill="#fff" fill-rule="evenodd"
            d="M50 6C52.6 6 55 7 57 8.8L90 38C92 39.8 93 42 93 44.5L93 78C93 86 86.6 92 79 92L21 92C13.4 92 7 86 7 78L7 44.5C7 42 8 39.8 10 38L43 8.8C45 7 47.4 6 50 6ZM41 92L41 66C41 61.6 44.6 58 49 58L51 58C55.4 58 59 61.6 59 66L59 92Z"/>
    </svg>
  </div>
  <button type="button" class="splash__skip" data-splash-skip>Pular</button>
</div>
<script src="<?= $base ?>/frontend/js/splash.js"></script>
