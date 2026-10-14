<?php
declare(strict_types=1);

namespace Services;

use Core\Database;
use Middleware\AuthMiddleware;

class DashboardService
{
    public static function stats(): array
    {
        AuthMiddleware::requireAgent();

        $total = (int) Database::fetch("SELECT COUNT(*) c FROM properties")['c'];
        $available = (int) Database::fetch("SELECT COUNT(*) c FROM properties WHERE status = 'disponivel'")['c'];
        $sold = (int) Database::fetch("SELECT COUNT(*) c FROM properties WHERE status = 'vendido'")['c'];
        $rented = (int) Database::fetch("SELECT COUNT(*) c FROM properties WHERE status = 'alugado'")['c'];
        $featured = (int) Database::fetch("SELECT COUNT(*) c FROM properties WHERE featured = 1")['c'];

        $leadsTotal = (int) Database::fetch("SELECT COUNT(*) c FROM leads")['c'];
        $leadsToday = (int) Database::fetch("SELECT COUNT(*) c FROM leads WHERE DATE(created_at) = CURDATE()")['c'];

        $agents = (int) Database::fetch("SELECT COUNT(*) c FROM users WHERE role_id = 2 AND status = 'active'")['c'];
        $users  = (int) Database::fetch("SELECT COUNT(*) c FROM users")['c'];

        return [
            'total_properties' => $total,
            'available'        => $available,
            'sold'             => $sold,
            'rented'           => $rented,
            'featured'         => $featured,
            'leads_total'      => $leadsTotal,
            'leads_today'      => $leadsToday,
            'active_agents'    => $agents,
            'users_total'      => $users,
        ];
    }

    public static function recentProperties(int $limit = 6): array
    {
        AuthMiddleware::requireAgent();

        return Database::fetchAll(
            "SELECT p.id, p.title, p.code, p.status, p.price, p.featured,
                    a.city, a.neighborhood,
                    (SELECT filename FROM property_images
                     WHERE property_id = p.id
                     ORDER BY is_primary DESC, sort_order ASC LIMIT 1) AS main_image
             FROM properties p
             LEFT JOIN property_addresses a ON a.property_id = p.id
             ORDER BY p.created_at DESC
             LIMIT ?",
            [$limit]
        );
    }

    public static function charts(): array
    {
        AuthMiddleware::requireAgent();

        $byStatus = Database::fetchAll(
            "SELECT status AS label, COUNT(*) AS value
             FROM properties GROUP BY status"
        );

        $byType = Database::fetchAll(
            "SELECT pt.name AS label, COUNT(*) AS value
             FROM properties p
             JOIN property_types pt ON pt.id = p.property_type_id
             GROUP BY pt.id"
        );

        $leadsByMonth = Database::fetchAll(
            "SELECT DATE_FORMAT(created_at, '%m/%Y') AS label, COUNT(*) AS value
             FROM leads
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
             GROUP BY DATE_FORMAT(created_at, '%Y-%m')
             ORDER BY MIN(created_at)"
        );

        return [
            'properties_by_status' => $byStatus,
            'properties_by_type'   => $byType,
            'leads_by_month'       => $leadsByMonth,
        ];
    }
}