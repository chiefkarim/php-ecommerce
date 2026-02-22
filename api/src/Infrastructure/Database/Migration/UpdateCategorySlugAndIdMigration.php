<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Migration;

use PDO;

final class UpdateCategorySlugAndIdMigration extends AbstractMigration
{
    public function up(PDO $pdo): void
    {
        $this->ensureCategorySlug($pdo);
        $this->ensureProductCategoryId($pdo);
        $this->dropLegacyCategoryName($pdo);
    }

    public function down(PDO $pdo): void
    {
        $this->restoreLegacyCategoryName($pdo);
        $this->dropProductCategoryId($pdo);
        $this->dropCategorySlug($pdo);
    }

    private function ensureCategorySlug(PDO $pdo): void
    {
        if (!$this->columnExists($pdo, 'categories', 'slug')) {
            $pdo->exec('ALTER TABLE categories ADD COLUMN slug VARCHAR(255) NULL');
        }

        $rows = $pdo->query('SELECT id, name, slug FROM categories ORDER BY id ASC')->fetchAll();
        $used = [];

        foreach ($rows as $row) {
            $id = (int) $row['id'];
            $name = (string) $row['name'];
            $existingSlug = (string) ($row['slug'] ?? '');

            if ($existingSlug !== '') {
                $used[$existingSlug] = true;
                continue;
            }

            $slug = $this->slugify($name);
            if ($slug === '') {
                $slug = 'category-' . $id;
            }

            if (isset($used[$slug])) {
                $slug = $slug . '-' . $id;
            }

            $used[$slug] = true;

            $statement = $pdo->prepare('UPDATE categories SET slug = :slug WHERE id = :id');
            $statement->execute(['slug' => $slug, 'id' => $id]);
        }

        if ($this->columnExists($pdo, 'categories', 'slug')) {
            $pdo->exec('ALTER TABLE categories MODIFY slug VARCHAR(255) NOT NULL');
        }

        if (!$this->indexExists($pdo, 'categories', 'uniq_categories_slug')) {
            $pdo->exec('CREATE UNIQUE INDEX uniq_categories_slug ON categories (slug)');
        }
    }

    private function ensureProductCategoryId(PDO $pdo): void
    {
        if (!$this->columnExists($pdo, 'products', 'category_id')) {
            $pdo->exec('ALTER TABLE products ADD COLUMN category_id INT UNSIGNED NULL');
        }

        if ($this->columnExists($pdo, 'products', 'category_name')) {
            $pdo->exec('UPDATE products p JOIN categories c ON p.category_name = c.name SET p.category_id = c.id WHERE p.category_id IS NULL');
        }

        if ($this->columnExists($pdo, 'products', 'category_id')) {
            $pdo->exec('ALTER TABLE products MODIFY category_id INT UNSIGNED NOT NULL');
        }

        if (!$this->indexExists($pdo, 'products', 'idx_products_category_id')) {
            $pdo->exec('CREATE INDEX idx_products_category_id ON products (category_id)');
        }

        if (!$this->foreignKeyExists($pdo, 'products', 'fk_products_category_id')) {
            $pdo->exec('ALTER TABLE products ADD CONSTRAINT fk_products_category_id FOREIGN KEY (category_id) REFERENCES categories(id)');
        }
    }

    private function dropLegacyCategoryName(PDO $pdo): void
    {
        if ($this->foreignKeyExists($pdo, 'products', 'fk_products_category_name')) {
            $pdo->exec('ALTER TABLE products DROP FOREIGN KEY fk_products_category_name');
        }

        if ($this->columnExists($pdo, 'products', 'category_name')) {
            $pdo->exec('ALTER TABLE products DROP COLUMN category_name');
        }
    }

    private function restoreLegacyCategoryName(PDO $pdo): void
    {
        if (!$this->columnExists($pdo, 'products', 'category_name')) {
            $pdo->exec('ALTER TABLE products ADD COLUMN category_name VARCHAR(255) NULL');
        }

        if ($this->columnExists($pdo, 'products', 'category_id')) {
            $pdo->exec('UPDATE products p JOIN categories c ON p.category_id = c.id SET p.category_name = c.name WHERE p.category_name IS NULL');
        }

        if ($this->columnExists($pdo, 'products', 'category_name')) {
            $pdo->exec('ALTER TABLE products MODIFY category_name VARCHAR(255) NOT NULL');
        }

        if (!$this->foreignKeyExists($pdo, 'products', 'fk_products_category_name')) {
            $pdo->exec('ALTER TABLE products ADD CONSTRAINT fk_products_category_name FOREIGN KEY (category_name) REFERENCES categories(name)');
        }
    }

    private function dropProductCategoryId(PDO $pdo): void
    {
        if ($this->foreignKeyExists($pdo, 'products', 'fk_products_category_id')) {
            $pdo->exec('ALTER TABLE products DROP FOREIGN KEY fk_products_category_id');
        }

        if ($this->indexExists($pdo, 'products', 'idx_products_category_id')) {
            $pdo->exec('DROP INDEX idx_products_category_id ON products');
        }

        if ($this->columnExists($pdo, 'products', 'category_id')) {
            $pdo->exec('ALTER TABLE products DROP COLUMN category_id');
        }
    }

    private function dropCategorySlug(PDO $pdo): void
    {
        if ($this->indexExists($pdo, 'categories', 'uniq_categories_slug')) {
            $pdo->exec('DROP INDEX uniq_categories_slug ON categories');
        }

        if ($this->columnExists($pdo, 'categories', 'slug')) {
            $pdo->exec('ALTER TABLE categories DROP COLUMN slug');
        }
    }

    private function columnExists(PDO $pdo, string $table, string $column): bool
    {
        $statement = $pdo->prepare(
            'SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_NAME = :column'
        );
        $statement->execute(['table' => $table, 'column' => $column]);

        return (int) $statement->fetchColumn() > 0;
    }

    private function indexExists(PDO $pdo, string $table, string $index): bool
    {
        $statement = $pdo->prepare(
            'SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND INDEX_NAME = :index'
        );
        $statement->execute(['table' => $table, 'index' => $index]);

        return (int) $statement->fetchColumn() > 0;
    }

    private function foreignKeyExists(PDO $pdo, string $table, string $constraint): bool
    {
        $statement = $pdo->prepare(
            "SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND CONSTRAINT_NAME = :constraint AND CONSTRAINT_TYPE = 'FOREIGN KEY'"
        );
        $statement->execute(['table' => $table, 'constraint' => $constraint]);

        return (int) $statement->fetchColumn() > 0;
    }

    private function slugify(string $name): string
    {
        $slug = strtolower($name);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';
        $slug = preg_replace('/(^-|-$)+/', '', $slug) ?? '';

        return $slug;
    }
}
