<?php
declare(strict_types=1);

namespace Models;

use Core\Database;

class Role
{
    public static function all(): array
    {
        return Database::fetchAll("SELECT * FROM roles ORDER BY id");
    }

    public static function find(int $id): ?array
    {
        return Database::fetch("SELECT * FROM roles WHERE id = ? LIMIT 1", [$id]);
    }

    public static function bySlug(string $slug): ?array
    {
        return Database::fetch("SELECT * FROM roles WHERE name = ? LIMIT 1", [$slug]);
    }
}