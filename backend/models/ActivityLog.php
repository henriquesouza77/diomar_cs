<?php
declare(strict_types=1);

namespace Models;

use Core\Database;

class ActivityLog
{
    public static function create(array $data): int
    {
        return Database::insert('activity_logs', [
            'user_id'    => $data['user_id'] ?? null,
            'action'     => $data['action'],
            'entity'     => $data['entity'] ?? null,
            'entity_id'  => $data['entity_id'] ?? null,
            'details'    => is_array($data['details'] ?? null)
                            ? json_encode($data['details'], JSON_UNESCAPED_UNICODE)
                            : ($data['details'] ?? null),
            'ip_address' => $data['ip'] ?? null,
            'user_agent' => $data['ua'] ?? null,
        ]);
    }

    public static function recent(int $limit = 50): array
    {
        return Database::fetchAll(
            "SELECT al.*, u.name AS user_name
             FROM activity_logs al
             LEFT JOIN users u ON u.id = al.user_id
             ORDER BY al.created_at DESC
             LIMIT ?",
            [$limit]
        );
    }
}