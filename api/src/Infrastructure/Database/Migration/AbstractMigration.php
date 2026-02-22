<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Migration;

use PDO;

abstract class AbstractMigration
{
    abstract public function up(PDO $pdo): void;

    abstract public function down(PDO $pdo): void;
}
