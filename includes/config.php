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

function get_current_user(): ?array {
    return $_SESSION['user'] ?? null;
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
