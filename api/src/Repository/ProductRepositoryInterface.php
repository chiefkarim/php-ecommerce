<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Product\AbstractProduct;

interface ProductRepositoryInterface
{
    /**
     * @return array<int, AbstractProduct>
     */
    public function findByCategory(?string $category): array;

    public function findById(string $productId): ?AbstractProduct;
}
