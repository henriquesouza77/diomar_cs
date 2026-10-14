<?php
/**
 * Componente: Modal genérico reutilizável.
 *
 * Uso:
 *   $modalId    = 'meu-modal';
 *   $modalTitle = 'Título';
 *   $modalBody  = '<p>Conteúdo HTML</p>';
 *   $modalFooter = '<button>Fechar</button>'; // opcional
 *   require __DIR__ . '/../components/modal.php';
 */
$modalId     = $modalId     ?? 'modal-' . uniqid();
$modalTitle  = $modalTitle  ?? '';
$modalBody   = $modalBody   ?? '';
$modalFooter = $modalFooter ?? '';
$modalClass  = $modalClass  ?? '';
?>
<div class="modal-overlay <?= htmlspecialchars($modalClass) ?>"
     id="<?= htmlspecialchars($modalId) ?>"
     role="dialog"
     aria-modal="true"
     aria-labelledby="<?= htmlspecialchars($modalId) ?>-title"
     aria-hidden="true">

  <div class="modal">
    <?php if ($modalTitle): ?>
      <header class="modal__header">
        <h3 class="modal__title" id="<?= htmlspecialchars($modalId) ?>-title">
          <?= htmlspecialchars($modalTitle) ?>
        </h3>
        <button type="button"
                class="modal__close"
                aria-label="Fechar modal"
                data-modal-close>
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
            <line x1="18" y1="6" x2="6" y2="18"/>
            <line x1="6" y1="6" x2="18" y2="18"/>
          </svg>
        </button>
      </header>
    <?php endif; ?>

    <div class="modal__body">
      <?= $modalBody ?>
    </div>

    <?php if ($modalFooter): ?>
      <footer class="modal__footer">
        <?= $modalFooter ?>
      </footer>
    <?php endif; ?>
  </div>
</div>