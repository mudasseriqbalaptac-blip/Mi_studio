<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_login();
$pageTitle = 'Settings';
$user = get_current_user();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verify_csrf($_POST['csrf_token'])) {
        flash('error', 'Invalid security token.');
        redirect(SITE_URL . '/social/settings.php');
    }
    $users = load_users();
    $email = strtolower($user['email']);
    $users[$email]['display_name'] = sanitize_text($_POST['display_name'] ?? $user['display_name']);
    $users[$email]['username'] = strtolower(preg_replace('/[^a-z0-9_]/i', '', sanitize_text($_POST['username'] ?? $user['username'])));
    $users[$email]['bio'] = sanitize_text($_POST['bio'] ?? $user['bio']);
    $users[$email]['website'] = sanitize_text($_POST['website'] ?? $user['website']);
    $users[$email]['location'] = sanitize_text($_POST['location'] ?? $user['location']);
    save_users($users);
    $_SESSION['user'] = normalize_user($users[$email], $email);
    flash('success', 'Profile updated successfully.');
    redirect(SITE_URL . '/social/profile.php');
}
include dirname(__DIR__) . '/includes/header.php';
?>
<section class="container section">
  <div class="form-card reveal">
    <h3>Profile settings</h3>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?php echo encode_output(csrf_token()); ?>">
      <div class="form-grid">
        <div><label>Display name</label><input name="display_name" value="<?php echo encode_output($user['display_name'] ?? $user['name'] ?? ''); ?>"></div>
        <div><label>Username</label><input name="username" value="<?php echo encode_output($user['username'] ?? ''); ?>"></div>
      </div>
      <label style="margin-top:1rem;">Bio</label>
      <textarea name="bio"><?php echo encode_output($user['bio'] ?? ''); ?></textarea>
      <div class="form-grid" style="margin-top:1rem;">
        <div><label>Website</label><input name="website" value="<?php echo encode_output($user['website'] ?? ''); ?>"></div>
        <div><label>Location</label><input name="location" value="<?php echo encode_output($user['location'] ?? ''); ?>"></div>
      </div>
      <button class="btn btn-primary" type="submit" style="margin-top:1rem;">Save changes</button>
    </form>
  </div>
</section>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
