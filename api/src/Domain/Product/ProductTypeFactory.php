<?php

declare(strict_types=1);

namespace App\Domain\Product;

final class ProductTypeFactory
{
    /**
     * @var array<string, callable(array<string, mixed>): AbstractProduct>
     */
    private array $builders;

    public function __construct()
    {
        $this->builders = [
            'clothes' => static fn (array $payload): AbstractProduct => new ApparelProduct(
                id: (string) $payload['id'],
                name: (string) $payload['name'],
                inStock: (bool) $payload['inStock'],
                gallery: $payload['gallery'],
                description: (string) $payload['description'],
                categoryId: (int) $payload['categoryId'],
                brand: (string) $payload['brand'],
                attributes: $payload['attributes'],
                prices: $payload['prices']
            ),
            'tech' => static fn (array $payload): AbstractProduct => new TechProduct(
                id: (string) $payload['id'],
                name: (string) $payload['name'],
                inStock: (bool) $payload['inStock'],
                gallery: $payload['gallery'],
                description: (string) $payload['description'],
                categoryId: (int) $payload['categoryId'],
                brand: (string) $payload['brand'],
                attributes: $payload['attributes'],
                prices: $payload['prices']
            ),
        ];
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function create(array $payload): AbstractProduct
    {
        $categorySlug = strtolower(trim((string) ($payload['categorySlug'] ?? '')));
        $builder = $this->builders[$categorySlug] ?? static fn (array $genericPayload): AbstractProduct => new GenericProduct(
            id: (string) $genericPayload['id'],
            name: (string) $genericPayload['name'],
            inStock: (bool) $genericPayload['inStock'],
            gallery: $genericPayload['gallery'],
            description: (string) $genericPayload['description'],
            categoryId: (int) $genericPayload['categoryId'],
            brand: (string) $genericPayload['brand'],
            attributes: $genericPayload['attributes'],
            prices: $genericPayload['prices']
        );

        return $builder($payload);
    }
}
