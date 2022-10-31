<?php

use Deivz\Aluraflix\controllers\ConnectionController;

require __DIR__ . '/../vendor/autoload.php';

$routes = require '../src/routes/routes.php';
$path = explode('/', $_SERVER['REQUEST_URI']);
$route = '/'.$path[1] ?? null;
$id = $path[2] ?? null;

if (!array_key_exists($route, $routes)){
    echo("Página não encontrada");
    exit();
}

$dbPath = __DIR__ . '/../src/database/database.sqlite';
$connection = new ConnectionController($dbPath);

$class = $routes[$route];
$controller = new $class($connection);
$controller->identifyRequest($_SERVER['REQUEST_METHOD'], $id);