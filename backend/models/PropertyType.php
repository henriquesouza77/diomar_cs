<?php
declare(strict_types=1);

namespace Models;

use Core\Database;

class PropertyType
{
    public static function all(?int $categoryId = null): array
    {
        if ($categoryId) {
            return Database::fetchAll(
                "SELECT * FROM property_types WHERE category_id = ? ORDER BY name",
                [$categoryId]
            );
        }
        return Database::fetchAll("SELECT * FROM property_types ORDER BY name");
    }

    public static function find(int $id): ?array
    {
        return Database::fetch("SELECT * FROM property_types WHERE id = ? LIMIT 1", [$id]);
    }
}