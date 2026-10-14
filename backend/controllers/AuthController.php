<?php
declare(strict_types=1);

namespace Controllers;

use Core\Request;
use Core\Response;
use Middleware\CsrfMiddleware;
use Services\AuthService;

class AuthController
{
    public function login(): void
    {
        CsrfMiddleware::handle();
        $data = Request::body();

        $result = AuthService::login(
            $data['email'] ?? '',
            $data['password'] ?? '',
            !empty($data['remember'])
        );

        Response::success($result, 'Login efetuado com sucesso.');
    }

    public function register(): void
    {
        CsrfMiddleware::handle();
        $result = AuthService::register(Request::body());
        Response::success($result, 'Conta criada com sucesso.');
    }

    public function logout(): void
    {
        AuthService::logout();

        // Suporta tanto API quanto redirect
        if (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false) {
            Response::success(null, 'Logout efetuado.');
        }
        Response::redirect('/diomar-cs/frontend/pages/login.php');
    }

    public function me(): void
    {
        $user = AuthService::me();
        if (!$user) Response::error('Não autenticado.', 401);
        Response::success($user);
    }
}