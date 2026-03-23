<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Seed;

use PDO;
use RuntimeException;

final class SeederRunner
{
    /**
     * @param string|null $onlyClassName Seeder class name without namespace.
     */
    public function run(PDO $pdo, ?string $onlyClassName = null): void
    {
        $seeders = $this->resolveSeeders();

        if ($onlyClassName !== null) {
            $className = $this->resolveClassName($onlyClassName);
            if (!isset($seeders[$className])) {
                throw new RuntimeException('Unknown seeder class: ' . $onlyClassName);
            }

            $this->runSeeder($pdo, $seeders[$className]);
            return;
        }

        $this->warnAndDelay('seeders');

        foreach (array_values($seeders) as $class) {
            $this->runSeeder($pdo, $class);
        }
    }

    /**
     * @return array<string, class-string<SeederInterface>>
     */
    private function resolveSeeders(): array
    {
        $directory = __DIR__;
        $files = glob($directory . '/*.php') ?: [];
        sort($files, SORT_STRING);

        $seeders = [];

        foreach ($files as $file) {
            $filename = basename($file);
            if (in_array($filename, ['SeederInterface.php', 'SeederRunner.php'], true)) {
                continue;
            }

            $className = pathinfo($filename, PATHINFO_FILENAME);
            $fqcn = $this->resolveClassName($className);

            if (!class_exists($fqcn)) {
                throw new RuntimeException('Seeder class not found: ' . $fqcn);
            }

            if (!is_subclass_of($fqcn, SeederInterface::class)) {
                throw new RuntimeException('Seeder must implement SeederInterface: ' . $fqcn);
            }

            $seeders[$className] = $fqcn;
        }

        if ($seeders === []) {
            throw new RuntimeException('No seeders found in ' . $directory);
        }

        return $seeders;
    }

    /**
     * @param class-string<SeederInterface> $class
     */
    private function runSeeder(PDO $pdo, string $class): void
    {
        $seeder = new $class();
        $seeder->run($pdo);
    }

    private function resolveClassName(string $className): string
    {
        return __NAMESPACE__ . '\\' . $className;
    }

    private function warnAndDelay(string $kind): void
    {
        fwrite(
            STDOUT,
            sprintf(
                "Warning: running all %s in 5 seconds. Press CTRL+C to abort.\n",
                $kind
            )
        );
        sleep(5);
    }
}
