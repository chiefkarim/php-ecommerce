<?php

declare(strict_types=1);

namespace App\Domain\Product;

final class ApparelProduct extends GenericProduct
{
    public function getTypeName(): string
    {
        return 'apparel';
    }
}
