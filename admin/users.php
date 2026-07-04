<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_admin();
$pageTitle = 'Manage Users';
$users = load_users();
include dirname(__DIR__) . '/includes/header.php';
?>
<section class="container section">
  <div class="section-header">
    <div>
      <div class="section-label">Admin</div>
      <h2>Manage users</h2>
    </div>
  </div>
  <div class="card-grid">
    <?php foreach ($users as $user): ?>
      <article class="info-card reveal">
        <strong><?php echo encode_output($user['display_name'] ?? $user['name'] ?? 'User'); ?></strong>
        <p class="muted"><?php echo encode_output($user['email'] ?? ''); ?></p>
        <div class="chip-row" style="margin-top:.8rem;">
          <span class="chip">Role: <?php echo encode_output($user['role'] ?? 'user'); ?></span>
          <span class="chip">Verified: <?php echo $user['verified'] ? 'Yes' : 'No'; ?></span>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
