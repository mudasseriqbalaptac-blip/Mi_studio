<?php
header('Content-Type: text/html; charset=UTF-8');

$rootDir = __DIR__;
$dataDir = $rootDir . DIRECTORY_SEPARATOR . 'data';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0777, true);
}

$submissionFile = $dataDir . DIRECTORY_SEPARATOR . 'submissions.json';

function sanitizeValue($value) {
    if (is_array($value)) {
        return array_map('sanitizeValue', $value);
    }

    return trim(strip_tags((string) $value));
}

function saveToJsonFile($entry, $filePath) {
    $existing = [];

    if (file_exists($filePath)) {
        $raw = file_get_contents($filePath);
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            $existing = $decoded;
        }
    }

    $existing[] = $entry;
    file_put_contents($filePath, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = [];

    foreach ($_POST as $key => $value) {
        $payload[$key] = sanitizeValue($value);
    }

    $entry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'source' => $payload['form_type'] ?? 'unknown',
        'data' => $payload
    ];

    saveToJsonFile($entry, $submissionFile);

    $dbName = getenv('DB_NAME') ?: 'mi_studio';
    $dbUser = getenv('DB_USER') ?: 'root';
    $dbPass = getenv('DB_PASS') ?: '';
    $dbHosts = [getenv('DB_HOST') ?: 'localhost', '127.0.0.1'];

    $dbError = null;
    $authStatus = 'failed';
    $authMessage = 'Something went wrong';
    $conn = null;

    mysqli_report(MYSQLI_REPORT_OFF);

    foreach ($dbHosts as $dbHost) {
        try {
            $conn = @new mysqli($dbHost, $dbUser, $dbPass);

            if ($conn->connect_error) {
                $dbError = $conn->connect_error;
                $conn->close();
                $conn = null;
                continue;
            }

            $conn->query("CREATE DATABASE IF NOT EXISTS `$dbName`");
            $conn->select_db($dbName);

            $conn->query(
                "CREATE TABLE IF NOT EXISTS users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    full_name VARCHAR(100) NOT NULL,
                    email VARCHAR(255) NOT NULL UNIQUE,
                    password VARCHAR(255) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )"
            );

            $submissionTable = 'mi_studio_submissions';
            $conn->query(
                "CREATE TABLE IF NOT EXISTS `$submissionTable` (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    form_type VARCHAR(50) NOT NULL,
                    payload TEXT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )"
            );

            $stmt = $conn->prepare("INSERT INTO `$submissionTable` (form_type, payload) VALUES (?, ?)");
            $stmt->bind_param('ss', $entry['source'], json_encode($payload, JSON_UNESCAPED_SLASHES));
            $stmt->execute();
            $stmt->close();

            $formType = $payload['form_type'] ?? 'unknown';
            $email = $payload['email'] ?? '';
            $password = $payload['password'] ?? '';

            if ($formType === 'signup') {
                $fullName = $payload['full_name'] ?? '';
                $checkStmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
                $checkStmt->bind_param('s', $email);
                $checkStmt->execute();
                $checkResult = $checkStmt->get_result();

                if ($checkResult->num_rows > 0) {
                    $authStatus = 'signup-existing';
                    $authMessage = 'Account already exists';
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $insertStmt = $conn->prepare('INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)');
                    $insertStmt->bind_param('sss', $fullName, $email, $hashedPassword);
                    $insertStmt->execute();
                    $insertStmt->close();
                    $authStatus = 'signup-success';
                    $authMessage = 'Account created successfully';
                }

                $checkStmt->close();
            } elseif ($formType === 'login') {
                $userStmt = $conn->prepare('SELECT password FROM users WHERE email = ?');
                $userStmt->bind_param('s', $email);
                $userStmt->execute();
                $userResult = $userStmt->get_result();

                if ($userResult->num_rows === 1) {
                    $userRow = $userResult->fetch_assoc();
                    if (password_verify($password, $userRow['password'])) {
                        $authStatus = 'login-success';
                        $authMessage = 'Login successful';
                    } else {
                        $authStatus = 'login-failed';
                        $authMessage = 'Incorrect password';
                    }
                } else {
                    $authStatus = 'login-failed';
                    $authMessage = 'No account found';
                }

                $userStmt->close();
            }

            break;
        } catch (Exception $e) {
            $dbError = $e->getMessage();
        }
    }

    if ($conn) {
        $conn->close();
    }

    $redirectPage = 'home.html';
    $redirectQuery = ['saved' => '1', 'auth' => $authStatus];

    if ($dbError) {
        error_log('Database save failed: ' . $dbError);
        $redirectQuery['error'] = rawurlencode($dbError);
    }

    if ($authMessage) {
        $redirectQuery['message'] = rawurlencode($authMessage);
    }

    header("Location: $redirectPage?" . http_build_query($redirectQuery));
    exit;
}

header('Location: home.html');
exit;
