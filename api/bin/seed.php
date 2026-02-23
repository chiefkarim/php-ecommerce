<?php

declare(strict_types=1);

use App\Infrastructure\Database\ConnectionFactory;
use App\Infrastructure\Database\Seed\SeederRunner;
use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable([
    __DIR__ . '/../',
    dirname(__DIR__, 2),
]);
$dotenv->safeLoad();

$pdo = (new ConnectionFactory())->create();

$onlyClass = $argv[1] ?? null;

$runner = new SeederRunner();
$runner->run($pdo, $onlyClass);
