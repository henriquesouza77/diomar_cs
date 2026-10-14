<?php
$pageTitle       = 'Fale conosco';
$pageDescription = 'Entre em contato com a DIOMAR_CS IMÓVEIS. Atendimento personalizado para compra, venda e aluguel.';
$currentPage     = 'contato';

require_once __DIR__ . '/../components/header.php';
$base = '/diomar-cs';
?>

<section class="contact-page">

  <div class="contact-hero">
    <div class="container contact-hero__inner">
      <span class="section__eyebrow" style="color:#93C5FD;">Estamos prontos para ajudar</span>
      <h1>Fale com a DIOMAR_CS</h1>
      <p>Tire suas dúvidas, agende uma visita ou receba uma consultoria personalizada.</p>
    </div>
  </div>

  <div class="container">
    <div class="contact-grid">

      <!-- Cards de contato -->
      <div class="contact-info">
        <div class="contact-card">
          <div class="contact-card__icon" aria-hidden="true">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          </div>
          <div class="contact-card__body">
            <h4>Telefone / WhatsApp</h4>
            <a href="tel:+5511999999999">(11) 99999-9999</a>
            <a href="https://wa.me/5511999999999" target="_blank" rel="noopener">Enviar mensagem no WhatsApp</a>
          </div>
        </div>

        <div class="contact-card">
          <div class="contact-card__icon" aria-hidden="true">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          </div>
          <div class="contact-card__body">
            <h4>E-mail</h4>
            <a href="mailto:contato@diomarcsimoveis.com.br">contato@diomarcsimoveis.com.br</a>
          </div>
        </div>

        <div class="contact-card">
          <div class="contact-card__icon" aria-hidden="true">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          </div>
          <div class="contact-card__body">
            <h4>Endereço</h4>
            <p>São Paulo — SP</p>
            <p>Atendimento de Segunda a Sexta, 9h às 18h</p>
          </div>
        </div>
      </div>

      <!-- Formulário -->
      <form class="contact-form" id="contactForm" aria-label="Formulário de contato">
        <h2 class="contact-form__title">Envie sua mensagem</h2>
        <p class="contact-form__subtitle">Retornaremos em até 24h úteis.</p>

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

        <div class="contact-form__row">
          <div class="form-group">
            <label class="form-label" for="contactName">Nome completo <span class="required">*</span></label>
            <input type="text" id="contactName" name="name" class="form-input" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="contactPhone">Telefone / WhatsApp <span class="required">*</span></label>
            <input type="tel" id="contactPhone" name="phone" class="form-input" required>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="contactEmail">E-mail <span class="required">*</span></label>
          <input type="email" id="contactEmail" name="email" class="form-input" required>
        </div>

        <div class="form-group">
          <label class="form-label" for="contactSubject">Assunto</label>
          <select id="contactSubject" name="subject" class="form-select">
            <option value="compra">Tenho interesse em comprar</option>
            <option value="venda">Quero vender meu imóvel</option>
            <option value="aluguel">Tenho interesse em alugar</option>
            <option value="anunciar">Quero anunciar meu imóvel</option>
            <option value="duvida">Outro assunto</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label" for="contactMessage">Mensagem <span class="required">*</span></label>
          <textarea id="contactMessage" name="message" class="form-textarea" rows="5" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary btn-lg btn-block">Enviar mensagem</button>
      </form>
    </div>
  </div>
</section>

<?php require __DIR__ . '/../components/footer.php'; ?>

<script>
  window.__PAGE__ = 'contato';
</script>