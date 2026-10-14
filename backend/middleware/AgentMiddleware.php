<?php
declare(strict_types=1);

namespace Middleware;

class AgentMiddleware
{
    public static function handle(): void
    {
        AuthMiddleware::requireAgent();
    }
}