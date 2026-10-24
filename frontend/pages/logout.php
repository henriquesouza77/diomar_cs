<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/backend/config/bootstrap.php';

\Core\Session::destroy();

header('Location: /diomar-cs/frontend/pages/index.php?logout=ok');
exit;