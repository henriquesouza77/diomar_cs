<?php
declare(strict_types=1);

namespace Models;

use Core\Database;

class LeadStatus
{
    public static function all(): array
    {
        return Database::fetchAll("SELECT * FROM lead_statuses ORDER BY id");
    }
}