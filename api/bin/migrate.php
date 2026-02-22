<?php

declare(strict_types=1);

use App\Infrastructure\Config\DotenvLoader;
use App\Infrastructure\Database\ConnectionFactory;
use App\Infrastructure\Database\Migration\InitialSchemaMigration;

require_once __DIR__ . '/../vendor/autoload.php';

DotenvLoader::boot([
    __DIR__ . '/../.env',
    dirname(__DIR__, 2) . '/.env',
]);

$pdo = (new ConnectionFactory())->create();
$schemaPath = __DIR__ . '/../database/schema.sql';

$mode = $argv[1] ?? 'up';

$migration = new InitialSchemaMigration();

if (in_array($mode, ['down', 'rollback', '--rollback'], true)) {
    $migration->rollback($pdo);
    fwrite(STDOUT, "Rollback completed successfully.\n");
    exit(0);
}

if ($mode !== 'up') {
    throw new RuntimeException('Unknown migration mode: ' . $mode);
}

$migration->migrate($pdo, $schemaPath);
fwrite(STDOUT, "Migration completed successfully.\n");
