<?php
/**
 * Componente: Skeleton loading.
 * Uso:
 *   $type = 'card' | 'text' | 'image' | 'avatar' | 'list';
 *   $count = 3;
 *   require __DIR__ . '/../components/skeleton.php';
 */
$type  = $type  ?? 'text';
$count = $count ?? 1;

for ($i = 0; $i < $count; $i++):

  switch ($type):

    // ==================== CARD ====================
    case 'card': ?>
      <article class="property-card-skeleton">
        <div class="skeleton skeleton-image"></div>
        <div class="property-card-skeleton__body">
          <div class="skeleton skeleton-title"></div>
          <div class="skeleton skeleton-text" style="width: 60%;"></div>
          <div class="skeleton skeleton-text" style="width: 80%;"></div>
          <div class="skeleton skeleton-text" style="width: 45%;"></div>
        </div>
      </article>
      <?php break;

    // ==================== IMAGE ====================
    case 'image': ?>
      <div class="skeleton skeleton-image" aria-hidden="true"></div>
      <?php break;

    // ==================== TITLE ====================
    case 'title': ?>
      <div class="skeleton skeleton-title" aria-hidden="true"></div>
      <?php break;

    // ==================== TEXT ====================
    case 'text': ?>
      <div class="skeleton skeleton-text" aria-hidden="true"></div>
      <?php break;

    // ==================== AVATAR ====================
    case 'avatar': ?>
      <div class="skeleton skeleton-avatar" aria-hidden="true"></div>
      <?php break;

    // ==================== LISTA ====================
    case 'list': ?>
      <div style="display:flex;gap:16px;align-items:center;padding:12px 0;" aria-hidden="true">
        <div class="skeleton skeleton-avatar"></div>
        <div style="flex:1;display:flex;flex-direction:column;gap:8px;">
          <div class="skeleton skeleton-text" style="width: 40%;"></div>
          <div class="skeleton skeleton-text" style="width: 70%;"></div>
        </div>
      </div>
      <?php break;

    // ==================== ROW (admin) ====================
    case 'row': ?>
      <tr aria-hidden="true">
        <td><div class="skeleton" style="height:36px;width:44px;border-radius:6px;"></div></td>
        <td>
          <div class="skeleton skeleton-text" style="width: 70%;"></div>
          <div class="skeleton skeleton-text" style="width: 40%;"></div>
        </td>
        <td><div class="skeleton skeleton-text" style="width: 60%;"></div></td>
        <td><div class="skeleton skeleton-text" style="width: 50%;"></div></td>
        <td><div class="skeleton skeleton-text" style="width: 30%;"></div></td>
      </tr>
      <?php break;

    default: ?>
      <div class="skeleton skeleton-text" aria-hidden="true"></div>
  <?php endswitch;

endfor;