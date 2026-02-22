<?php

declare(strict_types=1);

namespace App\Infrastructure\Database;

use PDO;
use RuntimeException;

final class InitialSchemaMigration
{
    public function migrate(PDO $pdo, string $schemaPath): void
    {
        $schemaSql = $this->loadSchemaSql($schemaPath);
        $pdo->exec($schemaSql);
    }

    public function rollback(PDO $pdo): void
    {
        $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
        $pdo->exec('DROP TABLE IF EXISTS order_item_selected_attributes');
        $pdo->exec('DROP TABLE IF EXISTS order_items');
        $pdo->exec('DROP TABLE IF EXISTS orders');
        $pdo->exec('DROP TABLE IF EXISTS prices');
        $pdo->exec('DROP TABLE IF EXISTS currencies');
        $pdo->exec('DROP TABLE IF EXISTS attribute_items');
        $pdo->exec('DROP TABLE IF EXISTS attribute_sets');
        $pdo->exec('DROP TABLE IF EXISTS product_galleries');
        $pdo->exec('DROP TABLE IF EXISTS products');
        $pdo->exec('DROP TABLE IF EXISTS categories');
        $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
    }

    private function loadSchemaSql(string $schemaPath): string
    {
        if (!file_exists($schemaPath)) {
            throw new RuntimeException('Schema file not found: ' . $schemaPath);
        }

        $schemaSql = file_get_contents($schemaPath);
        if (!is_string($schemaSql)) {
            throw new RuntimeException('Unable to read schema SQL');
        }

        return $schemaSql;
    }
}
