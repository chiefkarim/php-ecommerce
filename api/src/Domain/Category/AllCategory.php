<?php

declare(strict_types=1);

namespace App\Domain\Category;

final class AllCategory extends Category
{
    public function productFilterCategoryId(): ?int
    {
        return null;
    }

    public function isAggregateCategory(): bool
    {
        return true;
    }
}
