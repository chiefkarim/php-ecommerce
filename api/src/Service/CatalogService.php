<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Product\AbstractProduct;
use App\Repository\CategoryRepositoryInterface;
use App\Repository\CurrencyRepositoryInterface;
use App\Repository\ProductRepositoryInterface;

final class CatalogService
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly CurrencyRepositoryInterface $currencyRepository
    ) {
    }

    public function categories(): array
    {
        return $this->categoryRepository->findAll();
    }

    /**
     * @return array<int, AbstractProduct>
     */
    public function products(?int $categoryId): array
    {
        if ($categoryId === null) {
            return $this->productRepository->findByCategory(null);
        }

        $category = $this->categoryRepository->findById($categoryId);
        $resolvedCategoryId = $category?->productFilterCategoryId() ?? $categoryId;

        return $this->productRepository->findByCategory($resolvedCategoryId);
    }

    public function product(string $productId): ?AbstractProduct
    {
        return $this->productRepository->findById($productId);
    }

    public function currencies(): array
    {
        return $this->currencyRepository->findAll();
    }
}
