<?php
declare(strict_types=1);

function start_admin_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $isSecure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $isSecure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function admin_database(): PDO
{
    $host = getenv('HOTEL_DB_HOST') ?: 'localhost';
    $database = getenv('HOTEL_DB_NAME') ?: 'choise_hotel';
    $username = getenv('HOTEL_DB_USER') ?: 'root';
    $password = getenv('HOTEL_DB_PASSWORD');

    return new PDO(
        "mysql:host={$host};dbname={$database};charset=utf8mb4",
        $username,
        $password === false ? '' : $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
}

function admin_csrf_token(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function require_admin(): void
{
    $adminId = filter_var($_SESSION['admin_id'] ?? null, FILTER_VALIDATE_INT);
    if ($adminId === false || $adminId === null) {
        header('Location: login.php');
        exit;
    }

    $pdo = admin_database();
    $statement = $pdo->prepare(
        "SELECT first_name, last_name, role
         FROM admins
         WHERE admin_id = :admin_id AND account_status = 'active'
         LIMIT 1"
    );
    $statement->execute(['admin_id' => $adminId]);
    $admin = $statement->fetch();
    if (!$admin) {
        $_SESSION = [];
        session_regenerate_id(true);
        header('Location: login.php');
        exit;
    }

    $_SESSION['admin_name'] = trim($admin['first_name'] . ' ' . $admin['last_name']);
    $_SESSION['admin_role'] = $admin['role'];
}
