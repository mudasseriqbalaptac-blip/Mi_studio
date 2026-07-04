<?php
require_once dirname(__DIR__) . '/includes/config.php';
if (!is_logged_in() || !is_admin()) {
    flash('error', 'Administrator access required.');
    redirect(SITE_URL . '/login.php');
}
$pageTitle = 'Admin Dashboard';
$settings = load_settings();
$projects = ensure_json_file(DATA_DIR . '/projects.json', []);
$blog = ensure_json_file(DATA_DIR . '/blog.json', []);
$submissions = ensure_json_file(DATA_DIR . '/submissions.json', []);
include dirname(__DIR__) . '/includes/header.php';
?>
<section class="page-hero container">
  <h1>Control center</h1>
  <p>Manage the portfolio, projects, content, and messages from a calm, focused workspace.</p>
</section>
<section class="container admin-grid" style="padding-bottom:2rem;">
  <article class="info-card">
    <div class="section-label">Overview</div>
    <h3 style="margin:.4rem 0 .5rem;">Welcome back, <?php echo encode_output($_SESSION['user']['name'] ?? 'Admin'); ?></h3>
    <p class="muted">You can update launches, write posts, and keep the site polished without touching code.</p>
  </article>
  <article class="info-card">
    <div class="section-label">Quick stats</div>
    <div class="stats-grid" style="margin-top:1rem;">
      <div class="stat"><strong><?php echo count($projects); ?></strong><span>Projects</span></div>
      <div class="stat"><strong><?php echo count($blog); ?></strong><span>Blog posts</span></div>
      <div class="stat"><strong><?php echo count($submissions); ?></strong><span>Messages</span></div>
      <div class="stat"><strong>24/7</strong><span>Ready</span></div>
    </div>
  </article>
  <article class="info-card">
    <div class="section-label">Manage</div>
    <div class="badge-list" style="margin-top:1rem;">
      <span><a href="<?php echo SITE_URL; ?>/project_upload.php">Add project</a></span>
      <span><a href="<?php echo SITE_URL; ?>/blog.html">Review blog</a></span>
      <span><a href="<?php echo SITE_URL; ?>/contact.html">View contact</a></span>
      <span><a href="<?php echo SITE_URL; ?>/auth/logout.php">Logout</a></span>
    </div>
  </article>
  <article class="info-card">
    <div class="section-label">Site settings</div>
    <p class="muted">Current title: <?php echo encode_output($settings['site_title'] ?? 'Mi Studio'); ?></p>
    <p class="muted">Contact email: <?php echo encode_output($settings['contact_email'] ?? 'hello@mistudio.dev'); ?></p>
  </article>
</section>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
