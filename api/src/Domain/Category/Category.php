<?php

declare(strict_types=1);

namespace App\Domain\Category;

abstract class Category
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug
    ) {
    }

    abstract public function productFilterCategoryId(): ?int;

    abstract public function isAggregateCategory(): bool;
}
