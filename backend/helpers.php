<?php
/**
 * Funções utilitárias globais
 */
declare(strict_types=1);

// ============================================================
// RESPOSTAS JSON
// ============================================================
function json_ok($data = null, string $message = 'OK'): void {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data'    => $data,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function json_error(string $message, int $status = 400, array $extra = []): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array_merge([
        'success' => false,
        'error'   => $message,
    ], $extra), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// ============================================================
// AUTENTICAÇÃO
// ============================================================
function auth_user(): ?array {
    if (empty($_SESSION['user_id'])) return null;
    return [
        'id'    => (int) $_SESSION['user_id'],
        'name'  => $_SESSION['user_name']  ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'role'  => $_SESSION['user_role']  ?? 'client',
    ];
}

function auth_id(): ?int {
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

function auth_role(): ?string {
    return $_SESSION['user_role'] ?? null;
}

function require_login(): array {
    $u = auth_user();
    if (!$u) json_error('Autenticação necessária.', 401);
    return $u;
}

function require_admin(): array {
    $u = require_login();
    if ($u['role'] !== 'admin') {
        json_error('Acesso restrito a administradores.', 403);
    }
    return $u;
}

function require_agent(): array {
    $u = require_login();
    if (!in_array($u['role'], ['admin', 'agent'], true)) {
        json_error('Acesso restrito a corretores.', 403);
    }
    return $u;
}

// ============================================================
// CSRF
// ============================================================
function csrf_token(): string {
    return $_SESSION['csrf_token'] ?? '';
}

function validate_csrf(): void {
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) return;

    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

    if (!$token) {
        $raw = file_get_contents('php://input');
        $json = json_decode($raw, true);
        $token = $json['csrf_token'] ?? $_POST['csrf_token'] ?? null;
    }

    if (!$token || !hash_equals(csrf_token(), $token)) {
        json_error('Token CSRF inválido. Recarregue a página.', 419);
    }
}

// ============================================================
// LEITURA DE INPUT
// ============================================================
function input(): array {
    static $data = null;
    if ($data !== null) return $data;

    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    if ($method === 'GET') {
        $data = $_GET;
    } else {
        $raw  = file_get_contents('php://input');
        $json = json_decode($raw, true) ?: [];
        $data = array_merge($_GET, $_POST, $json);
    }
    return $data;
}

function input_str(string $key, string $default = ''): string {
    return trim((string) (input()[$key] ?? $default));
}

function input_int(string $key, int $default = 0): int {
    return (int) (input()[$key] ?? $default);
}

function input_float(string $key, float $default = 0): float {
    return (float) (input()[$key] ?? $default);
}

// ============================================================
// SANITIZAÇÃO
// ============================================================
function clean_email(?string $email): ?string {
    $email = filter_var(trim((string) $email), FILTER_VALIDATE_EMAIL);
    return $email ?: null;
}

function clean_phone(?string $phone): ?string {
    $digits = preg_replace('/\D/', '', (string) $phone);
    return $digits ?: null;
}

function clean_money($value): float {
    if (is_numeric($value)) return (float) $value;
    $value = preg_replace('/[^\d,.]/', '', (string) $value);
    $value = str_replace('.', '', $value);
    $value = str_replace(',', '.', $value);
    return (float) $value;
}

function slugify(string $text): string {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    return strtolower($text) ?: 'n-a';
}

// ============================================================
// HELPERS DIVERSOS
// ============================================================
function now(): string {
    return date('Y-m-d H:i:s');
}

function log_debug($msg): void {
    if (APP_DEBUG) {
        error_log('[DIOMAR] ' . (is_array($msg) ? json_encode($msg) : $msg));
    }
}

function ensure_upload_dir(): void {
    if (!is_dir(IMAGES_PATH)) {
        mkdir(IMAGES_PATH, 0755, true);
    }
}
/**
 * ================================================================
 * HELPERS BLOCO 1 — Código sequencial + Slug + Comissão
 * ================================================================
 */

/**
 * Gera código sequencial automático (1, 2, 3, 4...)
 */
function generate_property_code(): int
{
    $pdo = db();
    $stmt = $pdo->query(
        "SELECT COALESCE(MAX(CAST(code AS UNSIGNED)), 0) + 1
         FROM properties
         WHERE code REGEXP '^[0-9]+$'"
    );
    return (int) $stmt->fetchColumn();
}

/**
 * Gera slug único a partir de um título.
 * Ex: "Cobertura Itaim Bibi" → "cobertura-itaim-bibi"
 */
function generate_unique_slug(string $title, ?int $ignoreId = null): string
{
    $base = slugify($title);
    if ($base === '' || $base === 'n-a') {
        $base = 'imovel';
    }

    $slug = $base;
    $i = 1;

    while (true) {
        $sql = "SELECT id FROM properties WHERE slug = ?";
        $params = [$slug];

        if ($ignoreId) {
            $sql .= " AND id != ?";
            $params[] = $ignoreId;
        }

        $sql .= " LIMIT 1";
        $stmt = db()->prepare($sql);
        $stmt->execute($params);

        if (!$stmt->fetch()) {
            return $slug;
        }

        $i++;
        $slug = $base . '-' . $i;
    }
}

/**
 * Calcula comissão em reais.
 */
function calculate_commission(float $soldPrice, float $percent = 6.0): float
{
    return round($soldPrice * ($percent / 100), 2);
}

/**
 * Formata valor em reais.
 */
function format_brl(float $value): string
{
    return 'R$ ' . number_format($value, 2, ',', '.');
}