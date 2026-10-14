<?php
/**
 * Inicializa ambiente: constantes, timezone, sessão, autoload.
 * Deve ser o PRIMEIRO require de qualquer entry point.
 */
declare(strict_types=1);

require_once __DIR__ . '/constants.php';

$appConfig = require __DIR__ . '/app.php';

date_default_timezone_set($appConfig['timezone']);

if ($appConfig['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Autoload simples PSR-0 adaptado
spl_autoload_register(function (string $class) {
    $parts = explode('\\', $class);
    $namespace = array_shift($parts);         // Core, Controllers, Services...
    $file = BACKEND_PATH . '/' . strtolower($namespace) . '/' . implode('/', $parts) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// Helpers globais
require_once BACKEND_PATH . '/helpers/functions.php';
require_once BACKEND_PATH . '/helpers/sanitize.php';
require_once BACKEND_PATH . '/helpers/format.php';

// Sessão
\Core\Session::start();

return $appConfig;