<?php
require_once dirname(__DIR__) . '/includes/config.php';
$settings = load_settings();
$pageTitle = $pageTitle ?? $settings['site_title'];
$flash = get_flash();
?><!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Mi Studio is a premium portfolio for a professional web developer and UI/UX designer.">
  <meta property="og:title" content="Mi Studio | Premium Portfolio">
  <meta property="og:description" content="Premium portfolio for web development, UI/UX design and modern digital products.">
  <meta property="twitter:card" content="summary_large_image">
  <title><?php echo encode_output($pageTitle); ?> | Mi Studio</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/portfolio.css">
</head>
<body>
<div id="scroll-progress"></div>
<header class="site-header">
  <div class="container navbar">
    <a class="brand" href="<?php echo SITE_URL; ?>/home.html">
      <img src="<?php echo SITE_URL; ?>/mi-studio-logo.png" alt="Mi Studio logo">
      <span>Mi Studio</span>
    </a>
    <nav class="nav-links" aria-label="Primary navigation">
      <a href="<?php echo SITE_URL; ?>/home.html">Home</a>
      <a href="<?php echo SITE_URL; ?>/about.php">About</a>
      <a href="<?php echo SITE_URL; ?>/projects.php">Projects</a>
      <a href="<?php echo SITE_URL; ?>/blog.php">Blog</a>
      <a href="<?php echo SITE_URL; ?>/contact.php">Contact</a>
      <a class="nav-pill" href="<?php echo SITE_URL; ?>/login.php">Admin Login</a>
    </nav>
  </div>
</header>
<main>
<?php if ($flash): ?>
  <div class="container" style="padding-top:1.2rem;">
    <div class="flash <?php echo encode_output($flash['type']); ?>">
      <?php echo encode_output($flash['message']); ?>
    </div>
  </div>
<?php endif; ?>
