<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Seed;

use PDO;

abstract class AbstractSeeder
{
    abstract public function run(PDO $pdo): void;
}
