<?php

declare(strict_types=1);

namespace App\Domain\Category;

final class Category
{
    public function __construct(public readonly string $name)
    {
    }
}
