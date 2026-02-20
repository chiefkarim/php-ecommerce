<?php

declare(strict_types=1);

namespace App\Domain\Order;

final class OrderItemSelection
{
    public function __construct(
        public readonly string $attributeId,
        public readonly string $itemId
    ) {
    }
}
