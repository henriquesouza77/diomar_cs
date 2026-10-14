<?php
declare(strict_types=1);

namespace Models;

use Core\Database;

class Agent
{
    public static function create(int $userId, array $data): int
    {
        return Database::insert('agents', [
            'user_id'     => $userId,
            'creci'       => $data['creci'] ?? null,
            'bio'         => $data['bio'] ?? null,
            'specialties' => $data['specialties'] ?? null,
        ]);
    }

    public static function findByUserId(int $userId): ?array
    {
        return Database::fetch("SELECT * FROM agents WHERE user_id = ? LIMIT 1", [$userId]);
    }

    public static function withUser(int $userId): ?array
    {
        return Database::fetch(
            "SELECT a.*, u.name, u.email, u.phone, u.whatsapp, u.avatar, u.status
             FROM agents a
             JOIN users u ON u.id = a.user_id
             WHERE a.user_id = ? LIMIT 1",
            [$userId]
        );
    }

    public static function update(int $userId, array $data): int
    {
        return Database::update('agents', $data, 'user_id = :user_id', ['user_id' => $userId]);
    }

    public static function allActive(): array
    {
        return Database::fetchAll(
            "SELECT a.*, u.name, u.email, u.phone, u.whatsapp, u.avatar,
                    (SELECT COUNT(*) FROM properties p WHERE p.agent_id = u.id) AS properties_count
             FROM agents a
             JOIN users u ON u.id = a.user_id
             WHERE u.status = 'active'
             ORDER BY u.name"
        );
    }
}