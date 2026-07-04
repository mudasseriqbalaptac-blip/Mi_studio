<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_login();
$pageTitle = 'Explore';
include dirname(__DIR__) . '/includes/header.php';
?>
<section class="container section">
  <div class="section-header">
    <div>
      <div class="section-label">Explore</div>
      <h2>Trending topics, fresh creators, and community energy.</h2>
    </div>
  </div>
  <div class="card-grid">
    <article class="info-card reveal"><h3>Trending hashtags</h3><p class="muted">#UXDesign #AIProduct #ModernWeb</p></article>
    <article class="info-card reveal"><h3>Suggested users</h3><p class="muted">Maya Chen • Daniel Flores • Aisha Khan</p></article>
    <article class="info-card reveal"><h3>Popular media</h3><p class="muted">Curated visual highlights and community moments.</p></article>
  </div>
</section>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
