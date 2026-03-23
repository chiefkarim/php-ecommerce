<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Seed;

use PDO;

interface SeederInterface
{
    public function run(PDO $pdo): void;
}
