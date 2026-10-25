<?php
/**
 * Lê backend/.env (fora do Git) e expõe env(). Sem .env valem os padrões de
 * desenvolvimento local (XAMPP) e o modo de depuração fica DESLIGADO.
 * Modelo das chaves: backend/.env.example
 */
if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        static $vars = null;

        if ($vars === null) {
            $vars = [];
            $file = dirname(__DIR__) . '/.env';
            if (is_file($file)) {
                foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                    $line = trim($line);
                    if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) continue;
                    [$k, $v] = explode('=', $line, 2);
                    $vars[trim($k)] = trim($v, " \t\"'");
                }
            }
        }

        if (array_key_exists($key, $vars)) return $vars[$key];
        $fromServer = getenv($key);
        return $fromServer !== false ? $fromServer : $default;
    }
}

if (!defined('BASE_PATH')) {
    // Pasta do site na URL (ex.: /diomar-cs). Em domínio próprio, deixe vazio no .env.
    define('BASE_PATH', rtrim((string) env('APP_BASE_PATH', '/diomar-cs'), '/'));
}
