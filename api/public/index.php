<?php

declare(strict_types=1);

use App\Controller\GraphQL;
use App\Infrastructure\Config\DotenvLoader;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

require_once __DIR__ . '/../vendor/autoload.php';

DotenvLoader::boot([
    __DIR__ . '/../.env',
    dirname(__DIR__, 2) . '/.env',
]);

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowOrigin = false;

if ($origin !== '') {
    if (preg_match('/^http:\/\/localhost(?::\\d+)?$/', $origin) === 1) {
        $allowOrigin = true;
    } elseif (preg_match('/^https:\\/\\/([a-z0-9-]+\\.)*vercel\\.app$/i', $origin) === 1) {
        $allowOrigin = true;
    }
}

if ($allowOrigin) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Vary: Origin');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Max-Age: 86400');
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$dispatcher = FastRoute\simpleDispatcher(static function (RouteCollector $collector): void {
    $collector->post('/graphql', [GraphQL::class, 'handle']);
});

$routeInfo = $dispatcher->dispatch(
    $_SERVER['REQUEST_METHOD'] ?? 'GET',
    parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/'
);

switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        http_response_code(404);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['error' => ['message' => 'Route not found']], JSON_THROW_ON_ERROR);
        break;

    case Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['error' => ['message' => 'Method not allowed']], JSON_THROW_ON_ERROR);
        break;

    case Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        echo $handler($vars);
        break;
}
