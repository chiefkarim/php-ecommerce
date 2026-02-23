<?php

declare(strict_types=1);

use App\Infrastructure\Database\ConnectionFactory;
use App\Infrastructure\Database\Migration\MigrationRunner;
use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable([
    __DIR__ . '/../',
    dirname(__DIR__, 2),
]);
$dotenv->safeLoad();

$pdo = (new ConnectionFactory())->create();
$mode = $argv[1] ?? 'up';
$onlyClass = $argv[2] ?? null;

$runner = new MigrationRunner();

if (in_array($mode, ['down', 'rollback', '--rollback'], true)) {
    $runner->run($pdo, 'down', $onlyClass);
    fwrite(STDOUT, "Rollback completed successfully.\n");
    exit(0);
}

if ($mode !== 'up') {
    throw new RuntimeException('Unknown migration mode: ' . $mode);
}

$runner->run($pdo, 'up', $onlyClass);
fwrite(STDOUT, "Migration completed successfully.\n");
