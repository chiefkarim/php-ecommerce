<?php

declare(strict_types=1);

namespace App\Infrastructure\Config;

final class DotenvLoader
{
    /**
     * @param array<int, string> $paths
     */
    public static function boot(array $paths): void
    {
        foreach ($paths as $path) {
            self::loadFile($path);

            if (self::isPresent('DATABASE_URL')) {
                break;
            }
        }
    }

    private static function loadFile(string $path): void
    {
        if (!is_file($path) || !is_readable($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES);
        if (!is_array($lines)) {
            return;
        }

        foreach ($lines as $line) {
            $pair = self::parseLine($line);
            if ($pair === null) {
                continue;
            }

            [$key, $value] = $pair;

            if (self::isPresent($key)) {
                continue;
            }

            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
            putenv(sprintf('%s=%s', $key, $value));
        }
    }

    /**
     * @return array{0: string, 1: string}|null
     */
    private static function parseLine(string $line): ?array
    {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '#')) {
            return null;
        }

        if (str_starts_with($trimmed, 'export ')) {
            $trimmed = trim(substr($trimmed, 7));
        }

        $parts = explode('=', $trimmed, 2);
        if (count($parts) !== 2) {
            return null;
        }

        $key = trim($parts[0]);
        if ($key === '' || !preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $key)) {
            return null;
        }

        $rawValue = trim($parts[1]);
        $value = self::normalizeValue($rawValue);

        return [$key, $value];
    }

    private static function normalizeValue(string $value): string
    {
        if ($value === '') {
            return '';
        }

        $first = $value[0];
        $last = $value[strlen($value) - 1];

        if (($first === '"' && $last === '"') || ($first === '\'' && $last === '\'')) {
            return substr($value, 1, -1);
        }

        $withoutComment = preg_split('/\s+#/', $value, 2);

        return trim(is_array($withoutComment) ? $withoutComment[0] : $value);
    }

    private static function isPresent(string $key): bool
    {
        $value = $_ENV[$key] ?? getenv($key) ?: null;

        return is_string($value) && trim($value) !== '';
    }
}
