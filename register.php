<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Create account';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verify_csrf($_POST['csrf_token'])) {
        flash('error', 'Invalid security token.');
        redirect(SITE_URL . '/register.php');
    }

    $name = sanitize_text($_POST['name'] ?? '');
    $email = strtolower(sanitize_text($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $confirm = (string)($_POST['confirm_password'] ?? '');

    if ($name === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '' || $password !== $confirm) {
        flash('error', 'Please complete the form correctly.');
        redirect(SITE_URL . '/register.php');
    }

    $user = create_user([
        'name' => $name,
        'email' => $email,
        'password' => $password,
        'username' => $_POST['username'] ?? '',
    ]);

    if ($user) {
        flash('success', 'Account created. You can sign in now.');
        redirect(SITE_URL . '/login.php');
    }

    flash('error', 'That email already exists.');
    redirect(SITE_URL . '/register.php');
}

include __DIR__ . '/includes/header.php';
?>
<section class="auth-shell">
  <div class="auth-card">
    <h1>Create your account</h1>
    <p class="muted">Join a premium social experience built for creators and communities.</p>
    <form method="post" style="margin-top:1.2rem;">
      <input type="hidden" name="csrf_token" value="<?php echo encode_output(csrf_token()); ?>">
      <div class="form-grid">
        <div><label for="name">Display name</label><input id="name" name="name" required></div>
        <div><label for="username">Username</label><input id="username" name="username"></div>
      </div>
      <label for="email" style="margin-top:1rem;">Email</label>
      <input id="email" name="email" type="email" required>
      <label for="password" style="margin-top:1rem;">Password</label>
      <input id="password" name="password" type="password" required>
      <label for="confirm_password" style="margin-top:1rem;">Confirm password</label>
      <input id="confirm_password" name="confirm_password" type="password" required>
      <button class="btn btn-primary" type="submit" style="margin-top:1.2rem;">Create account</button>
    </form>
    <p class="small" style="margin-top:1rem;"><a href="<?php echo SITE_URL; ?>/login.php">Already have an account?</a></p>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
