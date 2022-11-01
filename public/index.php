<?php

declare(strict_types=1);

use Deivz\Aluraflix\controllers\ConnectionController;

require __DIR__ . '/../vendor/autoload.php';

set_error_handler("Deivz\Aluraflix\helpers\ErrorHandler::handleError");
set_exception_handler("Deivz\Aluraflix\helpers\ErrorHandler::handleException");

$routes = require '../src/routes/routes.php';
$path = explode('/', $_SERVER['REQUEST_URI']);
$route = '/' . $path[1] ?? null;
$id = $path[2] ?? null;

if (!array_key_exists($route, $routes)) {
    http_response_code(404);
    echo json_encode([
        'message' => 'Page not found',
        'code' => '404'
    ]);
    exit();
}

$dbPath = __DIR__ . '/../src/database/database.sqlite';
$connection = new ConnectionController($dbPath);

$class = $routes[$route];
$controller = new $class($connection);
$controller->identifyRequest($_SERVER['REQUEST_METHOD'], $id);