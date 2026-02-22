<?php

declare(strict_types=1);

namespace App\Domain\Category;

final class CategoryTypeFactory
{
    /**
     * @var array<string, callable(int, string, string): Category>
     */
    private array $builders;

    public function __construct()
    {
        $this->builders = [
            'all' => static fn (int $id, string $name, string $slug): Category => new AllCategory($id, $name, $slug),
        ];
    }

    public function create(int $id, string $name, string $slug): Category
    {
        $builder = $this->builders[$slug] ?? static fn (int $categoryId, string $categoryName, string $categorySlug): Category => new StandardCategory($categoryId, $categoryName, $categorySlug);

        return $builder($id, $name, $slug);
    }
}
