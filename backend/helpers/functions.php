<?php
/**
 * Funções utilitárias globais.
 */

if (!function_exists('config')) {
    function config(string $key, $default = null) {
        static $config = null;
        if ($config === null) $config = require BACKEND_PATH . '/config/app.php';

        $keys = explode('.', $key);
        $value = $config;
        foreach ($keys as $k) {
            if (!is_array($value) || !array_key_exists($k, $value)) return $default;
            $value = $value[$k];
        }
        return $value;
    }
}

if (!function_exists('env')) {
    function env(string $key, $default = null) {
        return $_ENV[$key] ?? getenv($key) ?: $default;
    }
}

if (!function_exists('base_url')) {
    function base_url(string $path = ''): string {
        return rtrim(config('url'), '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string {
        return base_url('frontend/' . ltrim($path, '/'));
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url): void {
        header('Location: ' . $url);
        exit;
    }
}

if (!function_exists('json_response')) {
    function json_response($data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}

if (!function_exists('json_error')) {
    function json_error(string $message, int $status = 400, array $extra = []): void {
        json_response(array_merge(['error' => $message], $extra), $status);
    }
}

if (!function_exists('uuid')) {
    function uuid(): string {
        return bin2hex(random_bytes(16));
    }
}

if (!function_exists('slugify')) {
    function slugify(string $text): string {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        return strtolower($text) ?: 'n-a';
    }
}

if (!function_exists('log_activity')) {
    function log_activity(string $action, ?string $entity = null, ?int $entityId = null, array $details = []): void {
        try {
            \Services\ActivityLogService::log($action, $entity, $entityId, $details);
        } catch (\Throwable $e) {
            // silencioso
        }
    }
}