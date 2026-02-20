<?php

declare(strict_types=1);

namespace App\Domain\Order;

final class OrderItemData
{
    /**
     * @param array<int, OrderItemSelection> $selectedAttributes
     */
    public function __construct(
        public readonly string $productId,
        public readonly int $quantity,
        public readonly float $unitAmount,
        public readonly array $selectedAttributes
    ) {
    }
}
