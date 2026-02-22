<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Migration;

use PDO;
use RuntimeException;

final class MigrationRunner
{
    /**
     * @param string|null $onlyClassName Migration class name without namespace.
     */
    public function run(PDO $pdo, string $direction, ?string $onlyClassName = null): void
    {
        $migrations = $this->resolveMigrations();

        if ($onlyClassName !== null) {
            $className = $this->resolveClassName($onlyClassName);
            if (!isset($migrations[$className])) {
                throw new RuntimeException('Unknown migration class: ' . $onlyClassName);
            }

            $this->runMigration($pdo, $direction, $migrations[$className]);
            return;
        }

        $this->warnAndDelay('migrations', $direction);

        $ordered = array_values($migrations);
        if ($direction === 'down') {
            $ordered = array_reverse($ordered);
        }

        foreach ($ordered as $class) {
            $this->runMigration($pdo, $direction, $class);
        }
    }

    /**
     * @return array<string, class-string<AbstractMigration>>
     */
    private function resolveMigrations(): array
    {
        $directory = __DIR__;
        $files = glob($directory . '/*.php') ?: [];
        sort($files, SORT_STRING);

        $migrations = [];

        foreach ($files as $file) {
            $filename = basename($file);
            if (in_array($filename, ['AbstractMigration.php', 'MigrationRunner.php'], true)) {
                continue;
            }

            $className = pathinfo($filename, PATHINFO_FILENAME);
            $fqcn = $this->resolveClassName($className);

            if (!class_exists($fqcn)) {
                throw new RuntimeException('Migration class not found: ' . $fqcn);
            }

            if (!is_subclass_of($fqcn, AbstractMigration::class)) {
                throw new RuntimeException('Migration must extend AbstractMigration: ' . $fqcn);
            }

            $migrations[$className] = $fqcn;
        }

        if ($migrations === []) {
            throw new RuntimeException('No migrations found in ' . $directory);
        }

        return $migrations;
    }

    /**
     * @param class-string<AbstractMigration> $class
     */
    private function runMigration(PDO $pdo, string $direction, string $class): void
    {
        $migration = new $class();

        if ($direction === 'up') {
            $migration->up($pdo);
            return;
        }

        if ($direction === 'down') {
            $migration->down($pdo);
            return;
        }

        throw new RuntimeException('Unknown migration direction: ' . $direction);
    }

    private function resolveClassName(string $className): string
    {
        return __NAMESPACE__ . '\\' . $className;
    }

    private function warnAndDelay(string $kind, string $direction): void
    {
        fwrite(
            STDOUT,
            sprintf(
                "Warning: running %s %s for all discovered items in 3 seconds. Press CTRL+C to abort.\n",
                $kind,
                $direction
            )
        );
        sleep(3);
    }
}
