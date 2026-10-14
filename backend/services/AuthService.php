<?php
declare(strict_types=1);

namespace Services;

use Core\Database;
use Core\Session;
use Core\Response;
use Models\User;
use Middleware\AuthMiddleware;

class AuthService
{
    public static function login(string $email, string $password, bool $remember = false): array
    {
        $email = clean_email($email);
        if (!$email || !$password) {
            Response::error('E-mail e senha são obrigatórios.', 422);
        }

        $user = User::findByEmail($email);
        if (!$user || !User::verifyPassword($password, $user['password_hash'])) {
            Response::error('E-mail ou senha incorretos.', 401);
        }

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