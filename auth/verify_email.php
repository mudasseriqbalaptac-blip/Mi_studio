<?php
require_once dirname(__DIR__) . '/includes/config.php';
$pageTitle = 'Verify Email';
$email = $_GET['email'] ?? '';
$token = $_GET['token'] ?? '';
$users = load_users();
if ($email !== '' && isset($users[$email])) {
    $users[$email]['email_verified'] = true;
    save_users($users);
    flash('success', 'Email verified successfully.');
    redirect(SITE_URL . '/login.php');
}
flash('error', 'The verification link is invalid or expired.');
redirect(SITE_URL . '/login.php');
