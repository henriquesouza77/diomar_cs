<?php
declare(strict_types=1);

namespace Services;

use Core\Database;
use Core\RateLimit;
use Core\Request;
use Core\Session;
use Core\Response;
use Models\User;
use Middleware\AuthMiddleware;

class AuthService
{
    public static function login(string $email, string $password): array
    {
        $email = clean_email($email);
        if (!$email || !$password) {
            Response::error('E-mail e senha são obrigatórios.', 422);
        }

        // Anti força bruta: por conta (várias origens) e por IP (várias contas)
        $max    = (int) config('login_max_attempts', 5);
        $window = (int) config('login_lockout_time', 900);
        $email  = strtolower($email);
        $ip     = Request::ip();
        RateLimit::enforce('login_email', $email, $max, $window);
        RateLimit::enforce('login_ip', $ip, $max * 4, $window);

        $user = User::findByEmail($email);
        if (!$user || !User::verifyPassword($password, $user['password_hash'])) {
            RateLimit::hit('login_email', $email);
            RateLimit::hit('login_ip', $ip);
            Response::error('E-mail ou senha incorretos.', 401);
        }
        RateLimit::clear('login_email', $email);

        if ($user['status'] !== 'active') {
            Response::error('Sua conta está inativa. Entre em contato com o suporte.', 403);
        }

        AuthMiddleware::login($user);
        User::touchLogin((int)$user['id']);

        ActivityLogService::log('login', 'user', (int)$user['id']);

        return [
            'user' => [
                'id'    => (int)$user['id'],
                'name'  => $user['name'],
                'email' => $user['email'],
                'role'  => $user['role_name'] ?? 'client',
            ],
        ];
    }

    public static function register(array $data): array
    {
        $name     = clean_str($data['name'] ?? '', 150);
        $email    = clean_email($data['email'] ?? '');
        $password = (string)($data['password'] ?? '');
        $passwordConfirm = (string)($data['password_confirm'] ?? '');
        $phone    = clean_phone($data['phone'] ?? null);

        RateLimit::enforce('register_ip', Request::ip(), 10, 3600);
        RateLimit::hit('register_ip', Request::ip());

        $errors = [];
        if (!$name)                                   $errors['name'] = 'Nome é obrigatório.';
        if (!$email)                                  $errors['email'] = 'E-mail inválido.';
        if (!$phone)                                  $errors['phone'] = 'Telefone é obrigatório.';
        if (strlen($password) < 8)                    $errors['password'] = 'Senha muito curta.';
        if ($passwordConfirm !== $password)
            $errors['password_confirm'] = 'As senhas não coincidem.';
        if ($email && User::findByEmail($email))      $errors['email'] = 'E-mail já cadastrado.';

        if ($errors) {
            Response::error('Erro de validação.', 422, ['fields' => $errors]);
        }

        $userId = User::create([
            'role_id'  => ROLE_CLIENT,
            'name'     => $name,
            'email'    => $email,
            'password' => $password,
            'phone'    => $phone,
        ]);

        ActivityLogService::log('register', 'user', $userId);

        return ['user_id' => $userId];
    }

    public static function loginWithGoogle(array $googleUser): array
    {
        $googleId = (string) ($googleUser['sub']     ?? '');
        $email    = clean_email($googleUser['email'] ?? '');
        $name     = clean_str($googleUser['name']    ?? '', 150) ?: 'Usuário Google';
        $avatar   = $googleUser['picture'] ?? null;

        if (!$googleId || !$email) {
            Response::error('Dados do Google incompletos.', 422);
        }
        if (!empty($googleUser['email_verified']) === false) {
            // Opcional — deixei permissivo. Descomente se quiser exigir email verificado:
            // Response::error('E-mail do Google não verificado.', 403);
        }

        // 1) Já logou com Google antes?
        $user = User::findByGoogleId($googleId);

        // 2) Senão, tenta pelo e-mail (vincular automaticamente — opção 1 que você escolheu)
        if (!$user) {
            $user = User::findByEmail($email);
            if ($user) {
                User::linkGoogleAccount((int) $user['id'], $googleId, $avatar);
            }
        }

        // 3) Senão, cria conta nova
        if (!$user) {
            $userId = User::create([
                'role_id'   => ROLE_CLIENT,
                'role'      => 'client',
                'name'      => $name,
                'email'     => $email,
                'google_id' => $googleId,
                'avatar'    => $avatar,
            ]);
            $user = User::find($userId);
            ActivityLogService::log('register_google', 'user', $userId);
        }

        if ($user['status'] !== 'active') {
             Response::error('Sua conta está inativa. Entre em contato com o suporte.', 403);
        }

        // Reutiliza exatamente o mesmo caminho de sessão do login normal
        AuthMiddleware::login($user);
        User::touchLogin((int) $user['id']);
        ActivityLogService::log('login_google', 'user', (int) $user['id']);

        return [
            'user' => [
            'id'    => (int) $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role_name'] ?? 'client',
            ],
        ];
    }

    public static function logout(): void
    {
        $userId = AuthMiddleware::userId();
        AuthMiddleware::logout();
        if ($userId) {
            ActivityLogService::log('logout', 'user', $userId);
        }
    }

    public static function me(): ?array
    {
        return AuthMiddleware::user();
    }
}