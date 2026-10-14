<?php
/**
 * Entry point único da API.
 * Todas as requisições passam por aqui via ?route=...
 */
declare(strict_types=1);

// Bootstrap (config, autoload, helpers, sessão)
require_once __DIR__ . '/../config/bootstrap.php';

use Core\Request;
use Core\Router;
use Core\Response;

// Rota e método
$route = (string) ($_GET['route'] ?? '');
$method = Request::method();

if (!$route) {
    Response::error('Nenhuma rota especificada.', 400);
}

// Carrega rotas
$routes = require __DIR__ . '/../routes/web.php';

// Roteador
$router = new Router();
$router->load($routes);

try {
    $router->dispatch($method, $route);
} catch (\Throwable $e) {
    error_log('[API] ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    $debug = config('debug');

    Response::json([
        'success' => false,
        'error'   => $debug ? $e->getMessage() : 'Erro interno do servidor.',
        'trace'   => $debug ? $e->getTraceAsString() : null,
    ], 500);
}