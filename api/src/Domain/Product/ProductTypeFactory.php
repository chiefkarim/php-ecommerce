<?php

declare(strict_types=1);

namespace App\Domain\Product;

final class ProductTypeFactory
{
    public function create(array $payload): AbstractProduct
    {
        return new CatalogProduct(
            id: (string) $payload['id'],
            name: (string) $payload['name'],
            inStock: (bool) $payload['inStock'],
            gallery: $payload['gallery'],
            description: (string) $payload['description'],
            categoryId: (int) $payload['categoryId'],
            brand: (string) $payload['brand'],
            attributes: $payload['attributes'],
            prices: $payload['prices']
        );
    }
}
