<?php
header('Content-Type: image/svg+xml');
header('Cache-Control: public, max-age=2592000');
readfile(__DIR__ . '/placeholder.svg');
exit;