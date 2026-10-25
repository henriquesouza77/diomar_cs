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
            $data['password'] ?? ''
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
        Response::redirect(BASE_PATH . '/frontend/pages/login.php');
    }

    public function me(): void
    {
        $user = AuthService::me();
        if (!$user) Response::error('Não autenticado.', 401);
        Response::success($user);
    }

    public function googleRedirect(): void
{
    $url = \Services\GoogleOAuthService::getAuthUrl();
    header('Location: ' . $url, true, 302);
    exit;
}

public function googleCallback(): void
{
    // 1) erro vindo do Google (usuário cancelou, etc.)
    if (!empty($_GET['error'])) {
        Response::redirect('/diomar-cs/frontend/pages/login.php?google=error');
    }

    // 2) valida state (anti-CSRF)
   if (!\Services\GoogleOAuthService::validateState($_GET['state'] ?? null)) {
        Response::redirect('/diomar-cs/frontend/pages/login.php?google=invalid_state');
    }

    $code = $_GET['code'] ?? '';
    if (!$code) {
        Response::redirect('/diomar-cs/frontend/pages/login.php?google=no_code');
    }

    try {
        $token    = \Services\GoogleOAuthService::exchangeCode($code);
        $profile  = \Services\GoogleOAuthService::fetchUserInfo($token['access_token']);
        $result   = \Services\AuthService::loginWithGoogle($profile);

        $role = $result['user']['role'] ?? 'client';
        $target = in_array($role, ['admin', 'agent'], true)
            ? '/diomar-cs/frontend/pages/admin/dashboard.php'
            : '/diomar-cs/frontend/pages/index.php';

        Response::redirect($target . '?google=ok');
    } catch (\Throwable $e) {
        error_log('[Google OAuth] ' . $e->getMessage());
        Response::redirect('/diomar-cs/frontend/pages/login.php?google=fail');
    }
}   
}
