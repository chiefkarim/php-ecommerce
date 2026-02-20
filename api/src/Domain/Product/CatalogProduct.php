<?php

declare(strict_types=1);

namespace App\Domain\Product;

final class CatalogProduct extends AbstractProduct
{
    public function getTypeName(): string
    {
        return 'Product';
    }
}
