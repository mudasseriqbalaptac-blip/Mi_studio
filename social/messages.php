<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_login();
$pageTitle = 'Messages';
$messages = load_messages();
include dirname(__DIR__) . '/includes/header.php';
?>
<section class="container section">
  <div class="card-grid" style="grid-template-columns: .8fr 1.2fr;">
    <article class="info-card reveal">
      <div class="section-label">Conversations</div>
      <div class="muted small" style="margin-top:1rem;">
        <div>• Maya Chen</div>
        <div>• Daniel Flores</div>
        <div>• Aisha Khan</div>
      </div>
    </article>
    <article class="form-card reveal">
      <div class="section-label">Chat</div>
      <div class="muted" style="margin-top:1rem;">
        <?php foreach ($messages as $message): ?>
          <div style="margin-bottom:.8rem;"><?php echo encode_output($message['text'] ?? ''); ?></div>
        <?php endforeach; ?>
      </div>
    </article>
  </div>
</section>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
