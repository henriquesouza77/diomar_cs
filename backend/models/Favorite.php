<?php
declare(strict_types=1);

namespace Models;

use Core\Database;

class Favorite
{
    public static function forUser(int $userId): array
    {
        return Database::fetchAll(
            "SELECT f.property_id, f.created_at,
                    p.title, p.code, p.price, p.purpose, p.status,
                    p.bedrooms, p.bathrooms, p.parking_spaces, p.area_built,
                    a.city, a.neighborhood,
                    (SELECT filename FROM property_images
                     WHERE property_id = p.id
                     ORDER BY is_primary DESC, sort_order ASC LIMIT 1) AS main_image
             FROM favorites f
             JOIN properties p ON p.id = f.property_id
             LEFT JOIN property_addresses a ON a.property_id = p.id
             WHERE f.user_id = ?
             ORDER BY f.created_at DESC",
            [$userId]
        );
    }

    public static function exists(int $userId, int $propertyId): bool
    {
        return (bool) Database::fetch(
            "SELECT 1 FROM favorites WHERE user_id = ? AND property_id = ? LIMIT 1",
            [$userId, $propertyId]
        );
    }

    public static function toggle(int $userId, int $propertyId): bool
    {
        if (self::exists($userId, $propertyId)) {
            Database::delete('favorites', 'user_id = ? AND property_id = ?', [$userId, $propertyId]);
            return false;
        }
        Database::insert('favorites', ['user_id' => $userId, 'property_id' => $propertyId]);
        return true;
    }

    public static function countForUser(int $userId): int
    {
        $row = Database::fetch("SELECT COUNT(*) AS c FROM favorites WHERE user_id = ?", [$userId]);
        return (int)($row['c'] ?? 0);
    }
}