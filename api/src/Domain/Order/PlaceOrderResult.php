<?php

declare(strict_types=1);

namespace App\Domain\Order;

final class PlaceOrderResult
{
    public function __construct(
        public readonly bool $success,
        public readonly ?int $orderId,
        public readonly string $message
    ) {
    }
}
