<?php
declare(strict_types=1);

namespace Core;

class Request
{
    private static array $jsonBody = [];
    private static bool $jsonBodyParsed = false;

    public static function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public static function isPost(): bool   { return self::method() === 'POST'; }
    public static function isGet(): bool    { return self::method() === 'GET'; }
    public static function isDelete(): bool { return self::method() === 'DELETE'; }
    public static function isPut(): bool    { return self::method() === 'PUT'; }

    public static function get(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    public static function post(string $key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }

    public static function input(string $key, $default = null)
    {
        $json = self::json();
        return $_POST[$key] ?? $json[$key] ?? $_GET[$key] ?? $default;
    }

    public static function all(): array
    {
        return array_merge($_GET, $_POST);
    }

    public static function json(): array
    {
        if (self::$jsonBodyParsed) return self::$jsonBody;

        $raw = file_get_contents('php://input');
        $data = $raw ? json_decode($raw, true) : [];
        self::$jsonBody = is_array($data) ? $data : [];
        self::$jsonBodyParsed = true;
        return self::$jsonBody;
    }

    public static function body(): array
    {
        $method = self::method();
        if ($method === 'GET') return $_GET;
        if ($method === 'POST' && !empty($_POST)) return $_POST;
        return array_merge(self::json(), $_POST, $_GET);
    }

    public static function file(string $key): ?array
    {
        return $_FILES[$key] ?? null;
    }

    public static function files(string $key): array
    {
        if (!isset($_FILES[$key])) return [];
        $files = $_FILES[$key];
        if (!is_array($files['name'])) {
            return [$files];
        }
        $result = [];
        foreach ($files['name'] as $i => $name) {
            $result[] = [
                'name'     => $name,
                'type'     => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error'    => $files['error'][$i],
                'size'     => $files['size'][$i],
            ];
        }
        return $result;
    }

    public static function ip(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '';
    }

    public static function userAgent(): string
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }
}