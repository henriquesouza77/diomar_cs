<?php
declare(strict_types=1);

namespace Controllers;

use Core\Response;
use Models\Agent;

class AgentController
{
    public function index(): void
    {
        Response::success(Agent::allActive());
    }
}