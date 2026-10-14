<?php
declare(strict_types=1);

namespace Models;

use Core\Database;

class Feature
{
    public static function all(): array
    {
        return Database::fetchAll("SELECT * FROM features ORDER BY name");
    }

    public static function forProperty(int $propertyId): array
    {
        return Database::fetchAll(
            "SELECT f.* FROM features f
             JOIN property_features pf ON pf.feature_id = f.id
             WHERE pf.property_id = ?
             ORDER BY f.name",
            [$propertyId]
        );
    }

    public static function create(string $name): int
    {
        return Database::insert('features', [
            'name' => $name,
            'slug' => slugify($name),
        ]);
    }

    public static function delete(int $id): int
    {
        return Database::delete('features', 'id = ?', [$id]);
    }
}