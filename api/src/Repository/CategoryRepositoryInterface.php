<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Category\Category;

interface CategoryRepositoryInterface
{
    /**
     * @return array<int, Category>
     */
    public function findAll(): array;
}
