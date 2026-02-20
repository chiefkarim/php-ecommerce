<?php

declare(strict_types=1);

namespace App\Domain\Product;

use App\Domain\Attribute\AbstractAttributeSet;
use App\Domain\Price\Price;

abstract class AbstractProduct
{
    /**
     * @param array<int, string> $gallery
     * @param array<int, AbstractAttributeSet> $attributes
     * @param array<int, Price> $prices
     */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly bool $inStock,
        public readonly array $gallery,
        public readonly string $description,
        public readonly string $category,
        public readonly string $brand,
        public readonly array $attributes,
        public readonly array $prices
    ) {
    }

    abstract public function getTypeName(): string;
}
