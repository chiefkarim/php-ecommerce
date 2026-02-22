<?php

declare(strict_types=1);

namespace App\Repository\MySql;

use App\Domain\Category\Category;
use App\Repository\CategoryRepositoryInterface;

final class MySqlCategoryRepository extends AbstractMySqlRepository implements CategoryRepositoryInterface
{
    public function findAll(): array
    {
        $statement = $this->pdo()->query('SELECT id, name, slug FROM categories ORDER BY id ASC');
        $rows = $statement !== false ? $statement->fetchAll() : [];

        return array_map(
            static fn (array $row): Category => new Category(
                (int) $row['id'],
                (string) $row['name'],
                (string) $row['slug']
            ),
            $rows
        );
    }
}
