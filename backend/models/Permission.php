<?php
declare(strict_types=1);

namespace Models;

use Core\Database;

class Permission
{
    public static function all(): array
    {
        return Database::fetchAll("SELECT * FROM permissions ORDER BY slug");
    }

    public static function forRole(int $roleId): array
    {
        return Database::fetchAll(
            "SELECT p.* FROM permissions p
             JOIN role_permissions rp ON rp.permission_id = p.id
             WHERE rp.role_id = ?",
            [$roleId]
        );
    }

    public static function roleHas(int $roleId, string $slug): bool
    {
        $row = Database::fetch(
            "SELECT 1 FROM role_permissions rp
             JOIN permissions p ON p.id = rp.permission_id
             WHERE rp.role_id = ? AND p.slug = ? LIMIT 1",
            [$roleId, $slug]
        );
        return (bool)$row;
    }
}