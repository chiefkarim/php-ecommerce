<?php

declare(strict_types=1);

namespace App\Repository\MySql;

use App\Domain\Category\Category;
use App\Domain\Category\CategoryTypeFactory;
use App\Repository\CategoryRepositoryInterface;

final class MySqlCategoryRepository extends AbstractMySqlRepository implements CategoryRepositoryInterface
{
    public function findAll(): array
    {
        $factory = new CategoryTypeFactory();
        $statement = $this->pdo()->query('SELECT id, name, slug FROM categories ORDER BY id ASC');
        $rows = $statement !== false ? $statement->fetchAll() : [];

        return array_map(
            static fn (array $row): Category => $factory->create(
                (int) $row['id'],
                (string) $row['name'],
                (string) $row['slug']
            ),
            $rows
        );
    }

    public function findById(int $categoryId): ?Category
    {
        $statement = $this->pdo()->prepare('SELECT id, name, slug FROM categories WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $categoryId]);

        $row = $statement->fetch();
        if (!is_array($row)) {
            return null;
        }

        return (new CategoryTypeFactory())->create(
            (int) $row['id'],
            (string) $row['name'],
            (string) $row['slug']
        );
    }
}
