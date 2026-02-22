<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Product\AbstractProduct;

interface ProductRepositoryInterface
{
    /**
     * @return array<int, AbstractProduct>
     */
    public function findByCategory(?int $categoryId): array;

    public function findById(string $productId): ?AbstractProduct;
}
