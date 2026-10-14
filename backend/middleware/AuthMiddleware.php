<?php
declare(strict_types=1);

namespace Middleware;

use Core\Session;
use Core\Response;

class AuthMiddleware
{
    public static function check(): bool
    {
        Session::start();
        return Session::has('user_id');
    }

    public static function user(): ?array
    {
        if (!self::check()) return null;
        return [
            'id'    => Session::get('user_id'),
            'name'  => Session::get('user_name'),
            'email' => Session::get('user_email'),
            'role'  => Session::get('user_role'),
            'role_id' => Session::get('user_role_id'),
        ];
    }

    public static function userId(): ?int
    {
        return Session::get('user_id');
    }

    public static function userRole(): ?int
    {
        return Session::get('user_role_id');
    }

    public static function isAdmin(): bool
    {
        return self::userRole() === ROLE_ADMIN;
    }

    public static function isAgent(): bool
    {
        return in_array(self::userRole(), [ROLE_ADMIN, ROLE_AGENT], true);
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            Response::error('Autenticação necessária.', 401);
        }
    }

    public static function requireRole(array $allowedRoles): void
    {
        self::requireLogin();
        $role = self::userRole();
        if (!in_array($role, $allowedRoles, true)) {
            Response::error('Acesso negado.', 403);
        }
    }

    public static function requireAdmin(): void
    {
        self::requireRole([ROLE_ADMIN]);
    }

    public static function requireAgent(): void
    {
        self::requireRole([ROLE_ADMIN, ROLE_AGENT]);
    }

    /** Login do usuário na sessão. */
    public static function login(array $user): void
    {
        Session::regenerate();
        Session::set('user_id',      (int)$user['id']);
        Session::set('user_name',    $user['name']);
        Session::set('user_email',   $user['email']);
        Session::set('user_role_id', (int)$user['role_id']);
        Session::set('user_role',    $user['role_name'] ?? 'client');
        Session::set('logged_at',    time());
    }

    public static function logout(): void
    {
        Session::destroy();
    }
}