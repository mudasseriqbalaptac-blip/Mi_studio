<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const SITE_ROOT = __DIR__ . '/..';
const SITE_URL = 'http://localhost/Mi_studio';
const DATA_DIR = SITE_ROOT . '/data';
const UPLOAD_DIR = SITE_ROOT . '/uploads/projects';
const DEFAULT_ADMIN_EMAIL = 'admin@mistudio.dev';
const DEFAULT_ADMIN_PASSWORD = 'Admin@2026!';

if (!is_dir(DATA_DIR)) {
    mkdir(DATA_DIR, 0777, true);
}

if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}

function ensure_json_file(string $path, array $default = []): array {
    if (!file_exists($path)) {
        file_put_contents($path, json_encode($default, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    $content = @file_get_contents($path);
    if ($content === false || trim($content) === '') {
        return $default;
    }

    $decoded = json_decode($content, true);
    return is_array($decoded) ? $decoded : $default;
}

function write_json_file(string $path, array $data): void {
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

function sanitize_text($value): string {
    return trim(strip_tags((string) $value));
}

function encode_output($value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array {
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function load_settings(): array {
    return ensure_json_file(DATA_DIR . '/settings.json', [
        'site_title' => 'Mi Studio',
        'site_tagline' => 'Premium web developer and UI/UX designer portfolio',
        'contact_email' => 'hello@mistudio.dev',
        'whatsapp' => '+1234567890',
        'linkedin' => 'https://www.linkedin.com',
        'github' => 'https://github.com',
        'instagram' => 'https://instagram.com',
    ]);
}

function save_settings(array $settings): void {
    write_json_file(DATA_DIR . '/settings.json', $settings);
}

function load_users(): array {
    $users = ensure_json_file(DATA_DIR . '/users.json', []);
    if (!is_array($users)) {
        $users = [];
    }

    if (!isset($users[DEFAULT_ADMIN_EMAIL])) {
        $users[DEFAULT_ADMIN_EMAIL] = [
            'id' => 1,
            'name' => 'Mi Studio Admin',
            'email' => DEFAULT_ADMIN_EMAIL,
            'password' => password_hash(DEFAULT_ADMIN_PASSWORD, PASSWORD_DEFAULT),
            'role' => 'admin',
            'provider' => 'local',
            'email_verified' => true,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        write_json_file(DATA_DIR . '/users.json', $users);
    }

    return $users;
}

function save_users(array $users): void {
    write_json_file(DATA_DIR . '/users.json', $users);
}

function find_user_by_email(string $email): ?array {
    $users = load_users();
    return $users[$email] ?? null;
}

function authenticate_user(string $email, string $password): ?array {
    $user = find_user_by_email($email);
    if (!$user) {
        return null;
    }

    if (!password_verify($password, $user['password'])) {
        return null;
    }

    return $user;
}

function normalize_user(array $user, string $email): array {
    $user['name'] = $user['name'] ?? ($user['display_name'] ?? 'User');
    $user['display_name'] = $user['display_name'] ?? $user['name'];
    $user['username'] = $user['username'] ?? strtolower(str_replace(' ', '', $user['display_name']));
    $user['email'] = $user['email'] ?? $email;
    $user['role'] = $user['role'] ?? 'user';
    $user['avatar'] = $user['avatar'] ?? 'mi-studio-logo.png';
    $user['cover'] = $user['cover'] ?? 'uploads/projects/project_6a460576ce65a.png';
    $user['bio'] = $user['bio'] ?? 'Crafting thoughtful digital experiences.';
    $user['verified'] = (bool)($user['verified'] ?? false);
    $user['email_verified'] = (bool)($user['email_verified'] ?? false);
    return $user;
}

function get_session_user(): ?array {
    return $_SESSION['user'] ?? null;
}

function get_authenticated_user(): ?array {
    return get_session_user();
}

function require_login(): void {
    if (!is_logged_in()) {
        flash('error', 'Please sign in to continue.');
        redirect(SITE_URL . '/login.php');
    }
}

function require_admin(): void {
    if (!is_admin()) {
        flash('error', 'Administrator access required.');
        redirect(SITE_URL . '/login.php');
    }
}

function create_user(array $data): ?array {
    $email = strtolower(sanitize_text($data['email'] ?? ''));
    $name = sanitize_text($data['name'] ?? '');
    $username = strtolower(preg_replace('/[^a-z0-9_]/i', '', sanitize_text($data['username'] ?? $name)));
    $password = (string)($data['password'] ?? '');

    if ($email === '' || $name === '' || $password === '') {
        return null;
    }

    $users = load_users();
    if (isset($users[$email])) {
        return null;
    }

    $user = [
        'id' => time(),
        'name' => $name,
        'display_name' => $name,
        'username' => $username !== '' ? $username : 'user' . time(),
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'role' => 'user',
        'provider' => 'local',
        'avatar' => 'mi-studio-logo.png',
        'cover' => 'uploads/projects/project_6a460576ce65a.png',
        'bio' => 'Building thoughtful digital spaces.',
        'website' => '',
        'location' => '',
        'verified' => false,
        'email_verified' => false,
        'followers' => 0,
        'following' => 0,
        'created_at' => date('Y-m-d H:i:s'),
        'verification_token' => bin2hex(random_bytes(16)),
    ];

    $users[$email] = $user;
    save_users($users);
    return $users[$email];
}

function load_posts(): array {
    $posts = ensure_json_file(DATA_DIR . '/posts.json', [
        [
            'id' => 1,
            'author' => 'admin@mistudio.dev',
            'name' => 'Mi Studio Admin',
            'avatar' => 'mi-studio-logo.png',
            'body' => 'Welcome to the new social experience—beautiful, secure and designed for real conversations.',
            'image' => 'uploads/projects/project_6a460576ce65a.png',
            'likes' => 14,
            'comments' => 4,
            'created_at' => date('Y-m-d H:i:s')
        ]
    ]);
    return $posts;
}

function save_posts(array $posts): void {
    write_json_file(DATA_DIR . '/posts.json', $posts);
}

function load_stories(): array {
    return ensure_json_file(DATA_DIR . '/stories.json', []);
}

function save_stories(array $stories): void {
    write_json_file(DATA_DIR . '/stories.json', $stories);
}

function load_notifications(): array {
    return ensure_json_file(DATA_DIR . '/notifications.json', []);
}

function save_notifications(array $notifications): void {
    write_json_file(DATA_DIR . '/notifications.json', $notifications);
}

function load_messages(): array {
    return ensure_json_file(DATA_DIR . '/messages.json', []);
}

function save_messages(array $messages): void {
    write_json_file(DATA_DIR . '/messages.json', $messages);
}

function record_failed_login(string $email): void {
    $attemptsFile = DATA_DIR . '/login_attempts.json';
    $attempts = ensure_json_file($attemptsFile, []);
    $now = time();
    $attempts[$email] = array_values(array_filter($attempts[$email] ?? [], function ($ts) use ($now) {
        return $ts > $now - 900;
    }));
    $attempts[$email][] = $now;
    write_json_file($attemptsFile, $attempts);
}

function is_login_locked(string $email): bool {
    $attemptsFile = DATA_DIR . '/login_attempts.json';
    $attempts = ensure_json_file($attemptsFile, []);
    $now = time();
    $recent = array_values(array_filter($attempts[$email] ?? [], function ($ts) use ($now) {
        return $ts > $now - 900;
    }));
    return count($recent) >= 5;
}

function clear_failed_login(string $email): void {
    $attemptsFile = DATA_DIR . '/login_attempts.json';
    $attempts = ensure_json_file($attemptsFile, []);
    unset($attempts[$email]);
    write_json_file($attemptsFile, $attempts);
}

function get_db_connection() {
    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $name = getenv('DB_NAME') ?: 'mi_studio';
    $user = getenv('DB_USER') ?: 'root';
    $pass = getenv('DB_PASS') ?: '';

    if (!extension_loaded('mysqli')) {
        return null;
    }

    $connection = @new mysqli($host, $user, $pass, $name);
    if ($connection->connect_error) {
        return null;
    }

    $connection->query("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(120) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(30) DEFAULT 'user',
        provider VARCHAR(30) DEFAULT 'local',
        email_verified TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $connection->query("CREATE TABLE IF NOT EXISTS submissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        type VARCHAR(40) NOT NULL,
        payload TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    return $connection;
}

function get_authenticated_user(): ?array {
    return get_session_user();
}

function is_admin(): bool {
    return ($_SESSION['user']['role'] ?? '') === 'admin';
}

function is_logged_in(): bool {
    return !empty($_SESSION['user']);
}

function redirect(string $path): void {
    header('Location: ' . $path);
    exit;
}
