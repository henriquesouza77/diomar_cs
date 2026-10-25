<?php
declare(strict_types=1);

namespace Services;

use Core\Response;
use Models\User;
use Models\Agent;
use Middleware\AuthMiddleware;

class UserService
{
    public static function list(array $filters): array
    {
        AuthMiddleware::requireAdmin();
        $page = max(1, (int)($filters['page'] ?? 1));
        return User::listAll($filters, $page, 20);
    }

    public static function createAgent(array $data): array
    {
        AuthMiddleware::requireAdmin();

        $name  = clean_str($data['name'] ?? '', 150);
        $email = clean_email($data['email'] ?? '');
        $pass  = (string)($data['password'] ?? bin2hex(random_bytes(6)));
        if (strlen($pass) < (int) config('password_min_length', 8)) {
            Response::error('A senha precisa ter ao menos 8 caracteres.', 422);
        }
        $phone = clean_phone($data['phone'] ?? null);

        if (!$name || !$email) {
            Response::error('Nome e e-mail são obrigatórios.', 422);
        }
        if (User::findByEmail($email)) {
            Response::error('E-mail já cadastrado.', 422);
        }

        $userId = User::create([
            'role_id'  => ROLE_AGENT,
            'name'     => $name,
            'email'    => $email,
            'password' => $pass,
            'phone'    => $phone,
        ]);

        Agent::create($userId, [
            'creci'       => $data['creci'] ?? null,
            'bio'         => $data['bio'] ?? null,
            'specialties' => $data['specialties'] ?? null,
        ]);

        ActivityLogService::log('user.create_agent', 'user', $userId);
        return ['id' => $userId, 'password' => $pass];
    }

    public static function update(int $id, array $data): void
    {
        AuthMiddleware::requireAdmin();

        $payload = [];
        if (!empty($data['name']))     $payload['name']  = clean_str($data['name'], 150);
        if (!empty($data['phone']))    $payload['phone'] = clean_phone($data['phone']);
        if (!empty($data['status'])) {
            if (!in_array($data['status'], ['active', 'inactive', 'banned'], true)) {
                Response::error('Status inválido.', 422);
            }
            $payload['status'] = $data['status'];
        }
        if (!empty($data['role_id'])) {
            if (!in_array((int)$data['role_id'], [ROLE_ADMIN, ROLE_AGENT, ROLE_CLIENT], true)) {
                Response::error('Perfil inválido.', 422);
            }
            $payload['role_id'] = (int)$data['role_id'];
        }
        if (!empty($data['password'])) {
            if (strlen((string)$data['password']) < (int) config('password_min_length', 8)) {
                Response::error('A senha precisa ter ao menos 8 caracteres.', 422);
            }
            $payload['password'] = $data['password'];
        }

        // Evita o admin se trancar para fora do próprio painel
        if ($id === AuthMiddleware::userId()
            && (($payload['role_id'] ?? ROLE_ADMIN) !== ROLE_ADMIN || ($payload['status'] ?? 'active') !== 'active')) {
            Response::error('Você não pode rebaixar ou desativar a própria conta.', 403);
        }

        User::update($id, $payload);
        ActivityLogService::log('user.update', 'user', $id);
    }

    public static function delete(int $id): void
    {
        AuthMiddleware::requireAdmin();
        if ($id === AuthMiddleware::userId()) {
            Response::error('Você não pode excluir sua própria conta.', 403);
        }
        User::delete($id);
        ActivityLogService::log('user.delete', 'user', $id);
    }

    public static function profile(int $id): ?array
    {
        return User::find($id);
    }
}