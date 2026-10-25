<?php
/**
 * Configuração central: constantes + banco + sessão
 */
declare(strict_types=1);

require_once __DIR__ . '/config/env.php';

// ============================================================
// CONSTANTES
// ============================================================
define('APP_NAME',    'DIOMAR_CS IMÓVEIS');
define('APP_URL',     env('APP_URL', 'http://localhost/diomar-cs'));
define('APP_DEBUG',   filter_var(env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN));
define('APP_TIMEZONE','America/Sao_Paulo');

// Caminhos
define('ROOT_PATH',      dirname(__DIR__));
define('BACKEND_PATH',   ROOT_PATH . '/backend');
define('FRONTEND_PATH',  ROOT_PATH . '/frontend');
define('UPLOADS_PATH',   BACKEND_PATH . '/uploads');
define('IMAGES_PATH',    UPLOADS_PATH . '/properties');

// Upload
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024);
define('UPLOAD_ALLOWED',  ['image/jpeg', 'image/png', 'image/webp']);

// Banco
define('DB_HOST',    env('DB_HOST', '127.0.0.1'));
define('DB_PORT',    env('DB_PORT', '3306'));
define('DB_NAME',    env('DB_NAME', 'diomar_cs_imoveis'));
define('DB_USER',    env('DB_USER', 'root'));
define('DB_PASS',    env('DB_PASS', ''));
define('DB_CHARSET', 'utf8mb4');

// ============================================================
// TIMEZONE + ERROS
// ============================================================
date_default_timezone_set(APP_TIMEZONE);

if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// ============================================================
// SESSÃO
// ============================================================
if (session_status() === PHP_SESSION_NONE) {
    session_name('diomar_session');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ============================================================
// CONEXÃO PDO
// ============================================================
function db(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
    );

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        error_log('[DB] ' . $e->getMessage());
        http_response_code(500);
        if (APP_DEBUG) {
            die('Erro de conexão: ' . $e->getMessage());
        }
        die('Erro interno do servidor.');
    }
    return $pdo;
}