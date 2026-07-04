<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_login();
$pageTitle = 'Profile';
$user = get_session_user();
$profile = normalize_user($user, $user['email'] ?? '');
include dirname(__DIR__) . '/includes/header.php';
?>
<section class="container section" style="padding-top:2rem;">
  <article class="hero-card reveal">
    <img src="<?php echo encode_output($profile['cover']); ?>" alt="cover" style="height:220px; width:100%; object-fit:cover; border-radius:18px;">
    <div class="panel-top" style="margin-top:1rem;">
      <div class="brand" style="gap:.8rem;">
        <img src="<?php echo encode_output($profile['avatar']); ?>" alt="avatar" style="width:70px; height:70px; border-radius:50%;">
        <div>
          <h3 style="margin:0;"><?php echo encode_output($profile['display_name']); ?></h3>
          <div class="muted">@<?php echo encode_output($profile['username']); ?></div>
        </div>
      </div>
      <a class="btn btn-primary" href="<?php echo SITE_URL; ?>/social/settings.php">Edit profile</a>
    </div>
    <p class="muted"><?php echo encode_output($profile['bio']); ?></p>
  </article>
  <div class="card-grid" style="margin-top:1.2rem;">
    <article class="info-card reveal">
      <div class="section-label">About</div>
      <p class="muted">Location: <?php echo encode_output($profile['location'] ?: 'Online'); ?></p>
      <p class="muted">Website: <?php echo encode_output($profile['website'] ?: '—'); ?></p>
      <p class="muted">Followers: <?php echo (int)$profile['followers']; ?></p>
      <p class="muted">Following: <?php echo (int)$profile['following']; ?></p>
    </article>
    <article class="info-card reveal">
      <div class="section-label">Privacy</div>
      <p class="muted">Account is public by default and ready for private mode updates.</p>
    </article>
  </div>
</section>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
