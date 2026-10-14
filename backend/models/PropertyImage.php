<?php
declare(strict_types=1);

namespace Models;

use Core\Database;

class PropertyImage
{
    public static function forProperty(int $propertyId): array
    {
        return Database::fetchAll(
            "SELECT * FROM property_images
             WHERE property_id = ?
             ORDER BY is_primary DESC, sort_order ASC",
            [$propertyId]
        );
    }

    public static function create(int $propertyId, array $data): int
    {
        return Database::insert('property_images', [
            'property_id' => $propertyId,
            'filename'    => $data['filename'],
            'is_primary'  => $data['is_primary'] ?? 0,
            'sort_order'  => $data['sort_order'] ?? 0,
            'alt_text'    => $data['alt_text'] ?? null,
        ]);
    }

    public static function delete(int $id): ?array
    {
        $image = Database::fetch("SELECT * FROM property_images WHERE id = ? LIMIT 1", [$id]);
        if (!$image) return null;

        Database::delete('property_images', 'id = ?', [$id]);
        return $image;
    }

    public static function setPrimary(int $propertyId, int $imageId): void
    {
        Database::update('property_images', ['is_primary' => 0], 'property_id = :pid', ['pid' => $propertyId]);
        Database::update('property_images', ['is_primary' => 1], 'id = :id', ['id' => $imageId]);
    }

    public static function reorder(array $imageIds): void
    {
        foreach ($imageIds as $i => $id) {
            Database::update('property_images', ['sort_order' => $i], 'id = :id', ['id' => (int)$id]);
        }
    }

    public static function deleteAllFromProperty(int $propertyId): array
    {
        $images = self::forProperty($propertyId);
        Database::delete('property_images', 'property_id = ?', [$propertyId]);
        return $images;
    }
}