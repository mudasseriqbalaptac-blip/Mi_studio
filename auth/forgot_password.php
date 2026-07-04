<?php
require_once dirname(__DIR__) . '/includes/config.php';
$pageTitle = 'Forgot Password';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verify_csrf($_POST['csrf_token'])) {
        flash('error', 'Invalid security token.');
        redirect(SITE_URL . '/auth/forgot_password.php');
    }

    $email = sanitize_text($_POST['email'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flash('error', 'Please enter a valid email address.');
        redirect(SITE_URL . '/auth/forgot_password.php');
    }

    $users = load_users();
    if (isset($users[$email])) {
        $token = bin2hex(random_bytes(16));
        $resetFile = DATA_DIR . '/password_resets.json';
        $resets = ensure_json_file($resetFile, []);
        $resets[$token] = ['email' => $email, 'created_at' => date('Y-m-d H:i:s')];
        write_json_file($resetFile, $resets);
        flash('success', 'A secure reset link was prepared for ' . $email . '.');
    } else {
        flash('success', 'If that address exists, a secure reset link has been prepared.');
    }

    redirect(SITE_URL . '/auth/forgot_password.php');
}

include dirname(__DIR__) . '/includes/header.php';
?>
<section class="auth-shell">
  <div class="auth-card">
    <h1 style="margin-bottom:.5rem;">Reset access</h1>
    <p class="muted">Enter your administrator email and we’ll prepare a secure recovery link.</p>
    <form method="post" style="margin-top:1.2rem;">
      <input type="hidden" name="csrf_token" value="<?php echo encode_output(csrf_token()); ?>">
      <label for="email">Email address</label>
      <input id="email" name="email" type="email" required>
      <button class="btn btn-primary" type="submit" style="margin-top:1rem;">Send recovery link</button>
    </form>
    <p class="small" style="margin-top:1rem;"><a href="<?php echo SITE_URL; ?>/login.php">Back to login</a></p>
  </div>
</section>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
