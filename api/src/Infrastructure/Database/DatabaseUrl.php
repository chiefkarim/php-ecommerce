<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use App\Infrastructure\Error\ConfigurationException;

final class DatabaseUrl
{
    public function __construct(
        public readonly string $host,
        public readonly int $port,
        public readonly string $database,
        public readonly string $username,
        public readonly string $password
    ) {
    }

    public static function fromString(string $databaseUrl): self
    {
        $parts = parse_url($databaseUrl);
        if (!is_array($parts)) {
            throw new ConfigurationException('Invalid DATABASE_URL format');
        }

        $scheme = $parts['scheme'] ?? null;
        if (!is_string($scheme) || !in_array($scheme, ['mysql', 'mysqli'], true)) {
            throw new ConfigurationException('DATABASE_URL must use mysql scheme');
        }

        $host = $parts['host'] ?? null;
        $path = $parts['path'] ?? null;
        $user = $parts['user'] ?? null;

        if (!is_string($host) || $host === '') {
            throw new ConfigurationException('DATABASE_URL host is required');
        }

        if (!is_string($path) || $path === '/' || $path === '') {
            throw new ConfigurationException('DATABASE_URL database name is required');
        }

        if (!is_string($user) || $user === '') {
            throw new ConfigurationException('DATABASE_URL username is required');
        }

        return new self(
            host: $host,
            port: is_int($parts['port'] ?? null) ? $parts['port'] : 3306,
            database: ltrim($path, '/'),
            username: $user,
            password: is_string($parts['pass'] ?? null) ? $parts['pass'] : ''
        );
    }
}
