<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Admin Login';

if (is_logged_in()) {
    redirect(SITE_URL . '/admin/dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verify_csrf($_POST['csrf_token'])) {
        flash('error', 'Invalid security token.');
        redirect(SITE_URL . '/login.php');
    }

    $email = sanitize_text($_POST['email'] ?? '');
    $password = (string)($_POST['password'] ?? '');
    $remember = !empty($_POST['remember']);

    $user = authenticate_user($email, $password);
    if ($user) {
        if (($user['role'] ?? 'user') === 'admin') {
            $_SESSION['user'] = normalize_user($user, $user['email']);
            if ($remember) {
                session_set_cookie_params(60 * 60 * 24 * 30);
            }
            clear_failed_login($email);
            flash('success', 'Welcome back, administrator.');
            redirect(SITE_URL . '/admin/dashboard.php');
        }

        $_SESSION['user'] = normalize_user($user, $user['email']);
        clear_failed_login($email);
        flash('success', 'Welcome back.');
        redirect(SITE_URL . '/social/feed.php');
    }

    record_failed_login($email);
    flash('error', 'Invalid credentials.');
    redirect(SITE_URL . '/login.php');
}

include __DIR__ . '/includes/header.php';
?>
<section class="auth-shell">
  <div class="auth-card">
    <div class="brand" style="margin-bottom:1rem;">
      <img src="<?php echo SITE_URL; ?>/mi-studio-logo.png" alt="Mi Studio logo">
      <div>
        <strong>Mi Studio Admin</strong>
        <div class="muted small">Private portfolio management</div>
      </div>
    </div>
    <h1 style="margin:.2rem 0 .4rem;">Secure sign in</h1>
    <p class="muted">Only administrators can access the control panel.</p>
    <form method="post" style="margin-top:1.2rem;">
      <input type="hidden" name="csrf_token" value="<?php echo encode_output(csrf_token()); ?>">
      <label for="email">Email</label>
      <input id="email" name="email" type="email" required autocomplete="email">
      <label for="password" style="margin-top:1rem;">Password</label>
      <input id="password" name="password" type="password" required autocomplete="current-password">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-top:1rem;">
        <label><input type="checkbox" name="remember" value="1"> Remember me</label>
        <a href="<?php echo SITE_URL; ?>/auth/forgot_password.php">Forgot password?</a>
      </div>
      <button class="btn btn-primary" type="submit" style="margin-top:1.2rem;">Sign in</button>
    </form>
    <div class="social-buttons">
      <a class="social-btn" href="#">Continue with Google</a>
      <a class="social-btn" href="#">Continue with GitHub</a>
    </div>
    <p class="small" style="margin-top:1rem; text-align:center;"><a href="<?php echo SITE_URL; ?>/register.php">Create an account</a></p>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
