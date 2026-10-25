<?php
declare(strict_types=1);

namespace Models;

use Core\Database;

class Lead
{
    public const TABLE = 'leads';

    public static function create(array $data): int
    {
        return Database::insert(self::TABLE, [
            'property_id' => $data['property_id'] ?? null,
            'agent_id'    => $data['agent_id'] ?? null,
            'status_id'   => $data['status_id'] ?? LEAD_STATUS_NEW,
            'name'        => $data['name'],
            'email'       => $data['email'],
            'phone'       => $data['phone'] ?? null,
            'whatsapp'    => $data['whatsapp'] ?? $data['phone'] ?? null,
            'message'     => $data['message'] ?? null,
            'source'      => $data['origin'] ?? 'site',
        ]);
    }

    public static function find(int $id): ?array
    {
        return Database::fetch("SELECT * FROM leads WHERE id = ? LIMIT 1", [$id]);
    }

    public static function listAll(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['q'])) {
            $where[] = '(l.name LIKE :q_name OR l.email LIKE :q_email OR p.title LIKE :q_title OR p.code LIKE :q_code)';
            $query = '%' . $filters['q'] . '%';
            $params['q_name'] = $query;
            $params['q_email'] = $query;
            $params['q_title'] = $query;
            $params['q_code'] = $query;
        }

        if (!empty($filters['agent_scope'])) {
            $where[] = '(l.agent_id = :agent_scope OR l.agent_id IS NULL)';
            $params['agent_scope'] = (int)$filters['agent_scope'];
        }

        if (!empty($filters['status_id'])) {
            $where[] = 'l.status_id = :status_id';
            $params['status_id'] = (int)$filters['status_id'];
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $offset = ($page - 1) * $perPage;

        $total = (int) Database::fetch(
            "SELECT COUNT(*) AS c
             FROM leads l
             LEFT JOIN properties p ON p.id = l.property_id
             {$whereSql}",
            $params
        )['c'];

        $params['limit'] = $perPage;
        $params['offset'] = $offset;

        $items = Database::fetchAll(
            "SELECT l.*,
                    p.title AS property_title,
                    p.code AS property_code,
                    ls.name AS status_name,
                    ls.color AS status_color
             FROM leads l
             LEFT JOIN properties p ON p.id = l.property_id
             LEFT JOIN lead_statuses ls ON ls.id = l.status_id
             {$whereSql}
             ORDER BY l.created_at DESC
             LIMIT :limit OFFSET :offset",
            $params
        );

        return ['items' => $items, 'total' => $total, 'pages' => max(1, (int)ceil($total / $perPage))];
    }

    public static function updateStatus(int $id, int $statusId): bool
    {
        return Database::update(self::TABLE, ['status_id' => $statusId], 'id = :id', ['id' => $id]) >= 0;
    }

    public static function countToday(): int
    {
        $row = Database::fetch(
            "SELECT COUNT(*) AS c FROM leads WHERE DATE(created_at) = CURDATE()"
        );
        return (int)($row['c'] ?? 0);
    }

    public static function countNew(): int
    {
        $row = Database::fetch(
            "SELECT COUNT(*) AS c FROM leads WHERE status_id = ?",
            [LEAD_STATUS_NEW]
        );
        return (int)($row['c'] ?? 0);
    }

    public static function byMonth(): array
    {
        return Database::fetchAll(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month,
                    COUNT(*) AS total
             FROM leads
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
             GROUP BY month
             ORDER BY month"
        );
    }
}