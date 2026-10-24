<?php
$base = $base ?? '/diomar-cs';
?>
<!-- ==================== FOOTER ==================== -->
<footer class="footer" role="contentinfo">
  <div class="container">

    <div class="footer__top">
      <div class="footer__brand">
        <a href="<?= $base ?>/frontend/pages/index.php" class="navbar__logo">
          <span class="navbar__logo-icon" aria-hidden="true">D</span>
          <span class="navbar__logo-text">
            <span class="navbar__logo-name">DIOMAR_CS</span>
            <span class="navbar__logo-tagline">IMÓVEIS</span>
          </span>
        </a>
        <p class="footer__about">
          Especialista em imóveis à venda e para alugar. Encontre o imóvel dos seus sonhos com atendimento personalizado.
        </p>
      </div>

      <div class="footer__columns">
        <div class="footer__column">
          <h4 class="footer__title">Imóveis</h4>
          <ul class="footer__list">
            <li><a href="<?= $base ?>/frontend/pages/imoveis.php?purpose=venda">Comprar</a></li>
            <li><a href="<?= $base ?>/frontend/pages/imoveis.php?purpose=aluguel">Alugar</a></li>
            <li><a href="<?= $base ?>/frontend/pages/imoveis.php?type=apartamento">Apartamentos</a></li>
            <li><a href="<?= $base ?>/frontend/pages/imoveis.php?type=casa">Casas</a></li>
            <li><a href="<?= $base ?>/frontend/pages/imoveis.php?type=terreno">Terrenos</a></li>
          </ul>
        </div>

        <div class="footer__column">
          <h4 class="footer__title">Institucional</h4>
          <ul class="footer__list">
            <li><a href="<?= $base ?>/frontend/pages/sobre.php">Quem somos</a></li>
            <li><a href="<?= $base ?>/frontend/pages/anunciar.php">Anuncie seu imóvel</a></li>
            <li><a href="<?= $base ?>/frontend/pages/contato.php">Fale conosco</a></li>
          </ul>
        </div>

        <div class="footer__column">
          <h4 class="footer__title">Serviços</h4>
          <ul class="footer__list">
            <li><a href="<?= $base ?>/frontend/pages/anunciar.php">Anunciar imóvel</a></li>
            <li><a href="<?= $base ?>/frontend/pages/imoveis.php?purpose=venda">Comprar imóvel</a></li>
            <li><a href="<?= $base ?>/frontend/pages/imoveis.php?purpose=aluguel">Alugar imóvel</a></li>
            <li><a href="<?= $base ?>/frontend/pages/contato.php">Simular financiamento</a></li>
          </ul>
        </div>

        <div class="footer__column">
          <h4 class="footer__title">Acompanhe-nos</h4>
          <ul class="footer__list">
            <li><a href="#" target="_blank" rel="noopener">Facebook</a></li>
            <li><a href="#" target="_blank" rel="noopener">Instagram</a></li>
            <li><a href="#" target="_blank" rel="noopener">LinkedIn</a></li>
          </ul>
        </div>
      </div>
    </div>

    <div class="footer__bottom">
      <p>&copy; <?= date('Y') ?> DIOMAR_CS IMÓVEIS. Todos os direitos reservados.</p>
      <div class="footer__legal">
        <a href="#">Termos de uso</a>
        <a href="#">Política de privacidade</a>
      </div>
    </div>
  </div>
</footer>

<!-- ==================== WHATSAPP FLUTUANTE ==================== -->
<a href="https://wa.me/5511999999999"
   class="whatsapp-float"
   target="_blank"
   rel="noopener"
   aria-label="Falar no WhatsApp">
  <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
  </svg>
</a>

<!-- ==================== BOTTOM NAV (mobile) ==================== -->
<nav class="bottom-nav" aria-label="Navegação inferior">
  <div class="bottom-nav__items">

    <a href="<?= $base ?>/frontend/pages/index.php"
       class="bottom-nav__item<?= $currentPage === 'home' ? ' is-active' : '' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="M3 9.5L12 3l9 6.5V20a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1z"/>
        <path d="M9 21v-7h6v7"/>
      </svg>
      <span>Início</span>
    </a>

    <a href="<?= $base ?>/frontend/pages/imoveis.php?purpose=venda"
       class="bottom-nav__item<?= $currentPage === 'imoveis' ? ' is-active' : '' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="11" cy="11" r="8"/>
        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
      </svg>
      <span>Buscar</span>
    </a>

    <a href="<?= $base ?>/frontend/pages/favoritos.php"
       class="bottom-nav__item<?= $currentPage === 'favoritos' ? ' is-active' : '' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
      </svg>
      <span>Favoritos</span>
      <span class="bottom-nav__badge" id="bottomFavBadge" hidden>0</span>
    </a>

    <?php if (!empty($_SESSION['user_id']) && in_array($_SESSION['user_role'] ?? 'client', ['admin', 'agent'], true)): ?>
      <a href="<?= $base ?>/frontend/pages/admin/dashboard.php"
         class="bottom-nav__item<?= $currentPage === 'admin' ? ' is-active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="3" width="7" height="9"/>
          <rect x="14" y="3" width="7" height="5"/>
          <rect x="14" y="12" width="7" height="9"/>
          <rect x="3" y="16" width="7" height="5"/>
        </svg>
        <span>Painel</span>
      </a>
    <?php else: ?>
      <a href="<?= $base ?>/frontend/pages/contato.php"
         class="bottom-nav__item<?= $currentPage === 'contato' ? ' is-active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
        <span>Contato</span>
      </a>
    <?php endif; ?>
  </div>
</nav>

<!-- ==================== PWA INSTALL BANNER ==================== -->
<div class="pwa-banner" id="pwaInstallBanner" role="dialog" aria-label="Instalar aplicativo">
  <div class="pwa-banner__icon" aria-hidden="true">D</div>
  <div class="pwa-banner__body">
    <div class="pwa-banner__title">Instalar DIOMAR_CS</div>
    <div class="pwa-banner__text" id="pwaInstallText">
      Adicione à sua tela inicial para acesso rápido
    </div>
  </div>
  <div class="pwa-banner__actions">
    <button type="button" class="pwa-banner__btn pwa-banner__btn--primary" id="pwaInstallBtn">
      Instalar
    </button>
    <button type="button" class="pwa-banner__btn pwa-banner__btn--ghost" id="pwaInstallClose" aria-label="Fechar">
      ✕
    </button>
  </div>
</div>

<!-- ==================== SCRIPTS ==================== -->
<script>window.__BASE_URL__ = '<?= $base ?>';</script>

<script src="<?= $base ?>/frontend/js/api.js" defer></script>
<script src="<?= $base ?>/frontend/js/motion.js" defer></script>
<script src="<?= $base ?>/frontend/js/animations.js" defer></script>
<script src="<?= $base ?>/frontend/js/navbar.js" defer></script>
<script src="<?= $base ?>/frontend/js/dock-nav.js" defer></script>
<script src="<?= $base ?>/frontend/js/hero-search.js" defer></script>
<script src="<?= $base ?>/frontend/js/properties.js" defer></script>
<script src="<?= $base ?>/frontend/js/favorites.js" defer></script>
<script src="<?= $base ?>/frontend/js/lottie-loader.js" defer></script>
<script src="<?= $base ?>/frontend/js/pwa.js" defer></script>
<script src="<?= $base ?>/frontend/js/main.js" defer></script>

<?php if (!empty($loadPropertyDetails)): ?>
  <script src="<?= $base ?>/frontend/js/property-details.js" defer></script>
<?php endif; ?>

<?php if (in_array($currentPage ?? '', ['login', 'cadastro'], true)): ?>
  <script src="<?= $base ?>/frontend/js/auth.js" defer></script>
<?php endif; ?>

<?php if (strpos($currentPage ?? '', 'admin') === 0 || ($currentPage ?? '') === 'admin'): ?>
  <script src="<?= $base ?>/frontend/js/admin.js" defer></script>
<?php endif; ?>

</body>
</html>