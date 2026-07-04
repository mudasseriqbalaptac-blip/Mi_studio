<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_login();
$pageTitle = 'Feed';
$user = get_current_user();
$posts = load_posts();
$stories = load_stories();
$notifications = load_notifications();
include dirname(__DIR__) . '/includes/header.php';
?>
<section class="container section" style="padding-top:2rem;">
  <div class="card-grid" style="grid-template-columns: 1.1fr .9fr; align-items:start;">
    <div>
      <article class="form-card reveal">
        <h3>Create a post</h3>
        <form method="post" action="<?php echo SITE_URL; ?>/social/post_create.php">
          <input type="hidden" name="csrf_token" value="<?php echo encode_output(csrf_token()); ?>">
          <textarea name="body" placeholder="What are you thinking about?" required></textarea>
          <div class="hero-actions" style="margin-top:1rem;">
            <button class="btn btn-primary" type="submit">Publish</button>
            <a class="btn btn-secondary" href="<?php echo SITE_URL; ?>/social/profile.php">View profile</a>
          </div>
        </form>
      </article>

      <div style="margin-top:1.2rem;">
        <?php foreach ($posts as $post): ?>
          <article class="project-card reveal" style="margin-bottom:1rem;">
            <div class="panel-top">
              <div class="brand" style="gap:.6rem;">
                <img src="<?php echo encode_output($post['avatar'] ?? 'mi-studio-logo.png'); ?>" alt="" style="width:44px; height:44px; border-radius:50%;">
                <div>
                  <strong><?php echo encode_output($post['name'] ?? 'User'); ?></strong>
                  <div class="muted small"><?php echo encode_output($post['created_at'] ?? ''); ?></div>
                </div>
              </div>
              <span class="chip">Popular</span>
            </div>
            <p><?php echo encode_output($post['body'] ?? ''); ?></p>
            <?php if (!empty($post['image'])): ?>
              <img src="<?php echo encode_output($post['image']); ?>" alt="post visual" style="border-radius:18px; height:240px; width:100%; object-fit:cover;">
            <?php endif; ?>
            <div class="chip-row" style="margin-top:.8rem;">
              <span class="chip">Like <?php echo (int)($post['likes'] ?? 0); ?></span>
              <span class="chip">Comments <?php echo (int)($post['comments'] ?? 0); ?></span>
              <span class="chip">Share</span>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    </div>

    <aside>
      <article class="info-card reveal">
        <div class="section-label">Stories</div>
        <div class="badge-list" style="margin-top:1rem;">
          <?php foreach ($stories as $story): ?>
            <span><?php echo encode_output($story['name'] ?? 'Story'); ?></span>
          <?php endforeach; ?>
        </div>
      </article>
      <article class="info-card reveal" style="margin-top:1rem;">
        <div class="section-label">Notifications</div>
        <div class="muted small" style="margin-top:.8rem;">
          <?php foreach ($notifications as $notification): ?>
            <div style="margin-bottom:.5rem;">• <?php echo encode_output($notification['message'] ?? ''); ?></div>
          <?php endforeach; ?>
        </div>
      </article>
      <article class="info-card reveal" style="margin-top:1rem;">
        <div class="section-label">Suggested people</div>
        <div class="muted small" style="margin-top:.8rem;">
          <div>• Maya Chen</div>
          <div>• Daniel Flores</div>
          <div>• Aisha Khan</div>
        </div>
      </article>
    </aside>
  </div>
</section>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
