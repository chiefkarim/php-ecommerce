<?php

declare(strict_types=1);

namespace App\Repository\MySql;

use App\Infrastructure\Database\ConnectionFactory;
use PDO;

abstract class AbstractMySqlRepository
{
    private static ?PDO $sharedPdo = null;

    protected function pdo(): PDO
    {
        if (self::$sharedPdo === null) {
            self::$sharedPdo = (new ConnectionFactory())->create();
        }

        return self::$sharedPdo;
    }
}
