<?php

require_once __DIR__ . '/../controllers/WareHouseController.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$routes = [
    'GET' => [
        '' => ['WarehouseController', 'index'],
        '/' => ['WarehouseController', 'index'],
        '/show' => ['WarehouseController', 'show'],
    ],
    'POST' => [
        '' => ['WarehouseController', 'store'],
        '/' => ['WarehouseController', 'store'],
    ],
    'PUT' => [
        '' => ['WarehouseController', 'update'],
        '/' => ['WarehouseController', 'update'],
    ],
    'DELETE' => [
        '' => ['WarehouseController', 'delete'],
        '/' => ['WarehouseController', 'delete'],
    ],
];

$method = $_SERVER['REQUEST_METHOD'];
$scriptName = $_SERVER['SCRIPT_NAME'];
$uri = str_replace($scriptName, '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

if ($method === 'GET' && isset($_GET['id'])) {
    (new WarehouseController())->show();
} elseif (isset($routes[$method][$uri])) {
    [$controller, $action] = $routes[$method][$uri];
    (new $controller())->$action();
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint not found']);
}