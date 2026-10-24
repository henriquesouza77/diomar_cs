<?php
declare(strict_types=1);

namespace Models;

use Core\Database;

class User
{
    public const TABLE = 'users';

    public static function find(int $id): ?array
    {
        return Database::fetch(
            "SELECT u.*, r.name AS role_name
             FROM users u
             LEFT JOIN roles r ON r.id = u.role_id
             WHERE u.id = ? LIMIT 1",
            [$id]
        );
    }

    public static function findByEmail(string $email): ?array
    {
        return Database::fetch(
            "SELECT u.*, r.name AS role_name
             FROM users u
             LEFT JOIN roles r ON r.id = u.role_id
             WHERE u.email = ? LIMIT 1",
            [$email]
        );
    }

    public static function create(array $data): int
    {
        $row = [
            'role_id'  => $data['role_id'] ?? ROLE_CLIENT,
            'role'     => $data['role']    ?? 'client',
            'name'     => $data['name'],
            'email'    => $data['email'],
            'status'   => $data['status']  ?? 'active',
            'phone'    => $data['phone']    ?? null,
            'whatsapp' => $data['whatsapp'] ?? $data['phone'] ?? null,
        ];

        if (!empty($data['password'])) {
            $row['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        if (!empty($data['google_id'])) {
            $row['google_id'] = $data['google_id'];
        }
        if (!empty($data['avatar'])) {
            $row['avatar'] = $data['avatar'];
        }

        return Database::insert(self::TABLE, $row);
    }

    public static function update(int $id, array $data): int
    {
        // Nunca deixa passar hash direto
        unset($data['password_hash']);

        if (!empty($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        unset($data['password']);

        return Database::update(self::TABLE, $data, 'id = :id', ['id' => $id]);
    }

    public static function delete(int $id): int
    {
        return Database::delete(self::TABLE, 'id = ?', [$id]);
    }

    public static function verifyPassword(string $plain, ?string $hash): bool{
    if ($hash === null || $hash === '') {
            return false;
        }
             return password_verify($plain, $hash);
    }

    public static function findByGoogleId(string $googleId): ?array
    {
        return Database::fetch(
            "SELECT u.*, r.name AS role_name
            FROM users u
            LEFT JOIN roles r ON r.id = u.role_id
            WHERE u.google_id = ? LIMIT 1",
                [$googleId]
        );
    }

    public static function linkGoogleAccount(int $userId, string $googleId, ?string $avatar = null): void
    {
        $data = ['google_id' => $googleId];
            if ($avatar) {
            $data['avatar'] = $avatar;
        }
        Database::update(self::TABLE, $data, 'id = :id', ['id' => $userId]);
    }


    public static function touchLogin(int $id): void
    {
        Database::update(self::TABLE, ['last_login' => date('Y-m-d H:i:s')], 'id = :id', ['id' => $id]);
    }

    public static function listAll(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['q'])) {
            $where[] = '(u.name LIKE :q_name OR u.email LIKE :q_email)';
            $query = '%' . $filters['q'] . '%';
            $params['q_name'] = $query;
            $params['q_email'] = $query;
        }
        if (!empty($filters['role_id'])) {
            $where[] = 'u.role_id = :role_id';
            $params['role_id'] = (int)$filters['role_id'];
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $offset = ($page - 1) * $perPage;

        $total = (int) Database::fetch(
            "SELECT COUNT(*) AS c FROM users u {$whereSql}",
            $params
        )['c'];

        $params['limit'] = $perPage;
        $params['offset'] = $offset;

        $items = Database::fetchAll(
            "SELECT u.*, r.name AS role_name
             FROM users u
             LEFT JOIN roles r ON r.id = u.role_id
             {$whereSql}
             ORDER BY u.created_at DESC
             LIMIT :limit OFFSET :offset",
            $params
        );

        return ['items' => $items, 'total' => $total];
    }
}