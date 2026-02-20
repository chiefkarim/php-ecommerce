<?php

declare(strict_types=1);

namespace App\Domain\Price;

use App\Domain\Currency\Currency;

final class Price
{
    public function __construct(
        public readonly float $amount,
        public readonly Currency $currency
    ) {
    }
}
