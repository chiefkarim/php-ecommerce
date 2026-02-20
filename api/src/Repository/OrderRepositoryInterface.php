<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Order\OrderItemData;

interface OrderRepositoryInterface
{
    /**
     * @param array<int, OrderItemData> $items
     */
    public function createOrder(float $totalAmount, array $items): int;
}
