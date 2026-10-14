<?php
declare(strict_types=1);

namespace Middleware;

class AdminMiddleware
{
    public static function handle(): void
    {
        AuthMiddleware::requireAdmin();
    }
}