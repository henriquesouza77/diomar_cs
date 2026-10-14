<?php
declare(strict_types=1);

namespace Services;

use Core\Request;
use Models\ActivityLog;
use Middleware\AuthMiddleware;

class ActivityLogService
{
    public static function log(string $action, ?string $entity = null, ?int $entityId = null, array $details = []): void
    {
        try {
            ActivityLog::create([
                'user_id'   => AuthMiddleware::userId(),
                'action'    => $action,
                'entity'    => $entity,
                'entity_id' => $entityId,
                'details'   => $details,
                'ip'        => Request::ip(),
                'ua'        => Request::userAgent(),
            ]);
        } catch (\Throwable $e) {
            error_log('[ActivityLog] ' . $e->getMessage());
        }
    }
}