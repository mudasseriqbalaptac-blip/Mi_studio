<?php
require_once dirname(__DIR__) . '/includes/config.php';
$pageTitle = 'Verify Email';
$email = strtolower(trim($_GET['email'] ?? ''));
$token = trim($_GET['token'] ?? '');
$users = load_users();
if (
    $email !== '' &&
    $token !== '' &&
    isset($users[$email]) &&
    isset($users[$email]['verification_token']) &&
    hash_equals($users[$email]['verification_token'], $token)
) {
    $users[$email]['email_verified'] = true;
    unset($users[$email]['verification_token']); // invalidate token after use
    save_users($users);
    flash('success', 'Email verified successfully.');
    redirect(SITE_URL . '/login.php');
}
flash('error', 'The verification link is invalid or expired.');
redirect(SITE_URL . '/login.php');
