<?php

declare(strict_types=1);

use App\Infrastructure\Config\DotenvLoader;
use App\Infrastructure\Database\ConnectionFactory;
use App\Infrastructure\Database\Seed\InitialCatalogSeeder;

require_once __DIR__ . '/../vendor/autoload.php';

DotenvLoader::boot([
    __DIR__ . '/../.env',
    dirname(__DIR__, 2) . '/.env',
]);

$pdo = (new ConnectionFactory())->create();
$dataPath = dirname(__DIR__, 2) . '/schema.json';

$seeder = new InitialCatalogSeeder();
$seeder->run($pdo, $dataPath);
