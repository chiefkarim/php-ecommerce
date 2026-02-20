<?php

declare(strict_types=1);

namespace App\Domain\Currency;

final class Currency
{
    public function __construct(
        public readonly string $label,
        public readonly string $symbol
    ) {
    }
}
