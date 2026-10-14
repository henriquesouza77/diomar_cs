<?php
declare(strict_types=1);

namespace Models;

use Core\Database;

class Category
{
    public static function all(): array
    {
        return Database::fetchAll("SELECT * FROM categories ORDER BY name");
    }

    public static function find(int $id): ?array
    {
        return Database::fetch("SELECT * FROM categories WHERE id = ? LIMIT 1", [$id]);
    }
}