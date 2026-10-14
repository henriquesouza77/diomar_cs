<?php
declare(strict_types=1);

namespace Models;

use Core\Database;

class Notification
{
    public static function create(int $userId, string $title, string $message, ?string $link = null): int
    {
        return Database::insert('notifications', [
            'user_id' => $userId,
            'title'   => $title,
            'message' => $message,
            'link'    => $link,
        ]);
    }

    public static function forUser(int $userId, int $limit = 20): array
    {
        return Database::fetchAll(
            "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?",
            [$userId, $limit]
        );
    }

    public static function unreadCount(int $userId): int
    {
        $row = Database::fetch(
            "SELECT COUNT(*) AS c FROM notifications WHERE user_id = ? AND is_read = 0",
            [$userId]
        );
        return (int)($row['c'] ?? 0);
    }

    public static function markRead(int $id, int $userId): void
    {
        Database::update('notifications', ['is_read' => 1], 'id = :id AND user_id = :uid', [
            'id' => $id, 'uid' => $userId,
        ]);
    }
}