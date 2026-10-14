<?php
declare(strict_types=1);

namespace Controllers;

use Core\Response;
use Services\DashboardService;

class DashboardController
{
    public function stats(): void
    {
        Response::success(DashboardService::stats());
    }

    public function recentProperties(): void
    {
        Response::success(['items' => DashboardService::recentProperties(6)]);
    }

    public function charts(): void
    {
        Response::success(DashboardService::charts());
    }
}