<?php

declare(strict_types=1);

namespace App\Infrastructure\Config;

use App\Infrastructure\Error\ConfigurationException;

final class Env
{
    public static function requireString(string $key): string
    {
        $value = $_ENV[$key] ?? getenv($key) ?: null;
        if (!is_string($value) || trim($value) === '') {
            throw new ConfigurationException(sprintf('Missing required environment variable: %s', $key));
        }

        return trim($value);
    }
}
