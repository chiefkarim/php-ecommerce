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
$mode = $argv[1] ?? 'up';

$migration = new InitialSchemaMigration();

if (in_array($mode, ['down', 'rollback', '--rollback'], true)) {
    $migration->down($pdo);
    fwrite(STDOUT, "Rollback completed successfully.\n");
    exit(0);
}

if ($mode !== 'up') {
    throw new RuntimeException('Unknown migration mode: ' . $mode);
}

    $migration->up($pdo);
    fwrite(STDOUT, "Migration completed successfully.\n");
