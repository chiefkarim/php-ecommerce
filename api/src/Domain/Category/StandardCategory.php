<?php

declare(strict_types=1);

namespace App\Domain\Category;

final class StandardCategory extends Category
{
    public function productFilterCategoryId(): ?int
    {
        return $this->id;
    }

    public function isAggregateCategory(): bool
    {
        return false;
    }
}
