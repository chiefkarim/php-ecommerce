<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Currency\Currency;

interface CurrencyRepositoryInterface
{
    /**
     * @return array<int, Currency>
     */
    public function findAll(): array;
}
