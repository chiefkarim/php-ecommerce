<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Infrastructure\Config\Env;
use App\Infrastructure\Error\DatabaseConnectionException;
use PDO;
use PDOException;

final class ConnectionFactory
{
    public function create(): PDO
    {
        $connection = DatabaseUrl::fromString(Env::requireString('DATABASE_URL'));

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $connection->host,
            $connection->port,
            $connection->database
        );

        try {
            return new PDO(
                $dsn,
                $connection->username,
                $connection->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $exception) {
            throw new DatabaseConnectionException(
                'Failed to connect to MySQL using DATABASE_URL',
                previous: $exception
            );
        }
    }
}
