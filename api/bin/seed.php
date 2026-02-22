<?php

declare(strict_types=1);

use App\Infrastructure\Database\ConnectionFactory;
use App\Infrastructure\Database\InitialDatabaseSeeder;
use App\Infrastructure\Config\DotenvLoader;

require_once __DIR__ . '/../vendor/autoload.php';

DotenvLoader::boot([
    __DIR__ . '/../.env',
    dirname(__DIR__, 2) . '/.env',
]);

$pdo = (new ConnectionFactory())->create();
$schemaPath = __DIR__ . '/../database/schema.sql';
$dataPath = dirname(__DIR__, 2) . '/schema.json';

$seeder = new InitialDatabaseSeeder();
$seeder->run($pdo, $schemaPath, $dataPath);
