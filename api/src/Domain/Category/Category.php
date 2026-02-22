<?php

declare(strict_types=1);

namespace App\Domain\Category;

final class Category
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug
    ) {
    }
}
