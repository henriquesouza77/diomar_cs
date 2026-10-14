<?php
declare(strict_types=1);

namespace Middleware;

use Core\Session;
use Core\Request;
use Core\Response;

class CsrfMiddleware
{
    public static function handle(): void
    {
        // Só valida métodos que alteram estado
        if (in_array(Request::method(), ['GET', 'HEAD', 'OPTIONS'], true)) return;

        // Pega token do header ou do body
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? Request::input('csrf_token');

        if (!Session::validateCsrf($token)) {
            Response::error('Token CSRF inválido ou expirado.', 419);
        }
    }
}