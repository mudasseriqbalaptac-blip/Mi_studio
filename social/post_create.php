<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(SITE_URL . '/social/feed.php');
}

if (!isset($_POST['csrf_token']) || !verify_csrf($_POST['csrf_token'])) {
    flash('error', 'Invalid security token.');
    redirect(SITE_URL . '/social/feed.php');
}

$body = sanitize_text($_POST['body'] ?? '');
if ($body === '') {
    flash('error', 'Your post needs a message.');
    redirect(SITE_URL . '/social/feed.php');
}

$posts = load_posts();
$user = get_session_user();
$posts[] = [
    'id' => time(),
    'author' => $user['email'],
    'name' => $user['display_name'] ?? $user['name'],
    'avatar' => $user['avatar'] ?? 'mi-studio-logo.png',
    'body' => $body,
    'image' => '',
    'likes' => 0,
    'comments' => 0,
    'created_at' => date('Y-m-d H:i:s')
];
save_posts($posts);
flash('success', 'Your post was published.');
redirect(SITE_URL . '/social/feed.php');
