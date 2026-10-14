<?php
declare(strict_types=1);

namespace Models;

use Core\Database;

class PropertyAddress
{
    public static function find(int $propertyId): ?array
    {
        return Database::fetch("SELECT * FROM property_addresses WHERE property_id = ? LIMIT 1", [$propertyId]);
    }
}