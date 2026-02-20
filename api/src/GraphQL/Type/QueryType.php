<?php

declare(strict_types=1);

namespace App\GraphQL\Type;

use App\Domain\Attribute\AbstractAttributeSet;
use App\Domain\Category\Category;
use App\Domain\Currency\Currency;
use App\Domain\Price\Price;
use App\Domain\Product\AbstractProduct;
use App\Service\CatalogService;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

final class QueryType
{
    public function __construct(
        private readonly CatalogService $catalogService,
        private readonly TypeRegistry $typeRegistry
    ) {
    }

    public function build(): ObjectType
    {
        return new ObjectType([
            'name' => 'Query',
            'fields' => [
                'health' => [
                    'type' => Type::string(),
                    'resolve' => static fn (): string => 'ok',
                ],
                'categories' => [
                    'type' => Type::nonNull($this->typeRegistry->listOfNonNull($this->typeRegistry->category())),
                    'resolve' => function (): array {
                        $categories = $this->catalogService->categories();

                        return array_map(
                            static fn (Category $category): array => ['name' => $category->name],
                            $categories
                        );
                    },
                ],
                'products' => [
                    'type' => Type::nonNull($this->typeRegistry->listOfNonNull($this->typeRegistry->product())),
                    'args' => [
                        'category' => ['type' => Type::string()],
                    ],
                    'resolve' => function ($rootValue, array $args): array {
                        $category = isset($args['category']) && is_string($args['category']) ? $args['category'] : null;

                        return array_map(
                            fn (AbstractProduct $product): array => $this->mapProduct($product),
                            $this->catalogService->products($category)
                        );
                    },
                ],
                'product' => [
                    'type' => $this->typeRegistry->product(),
                    'args' => [
                        'id' => ['type' => Type::nonNull(Type::string())],
                    ],
                    'resolve' => function ($rootValue, array $args): ?array {
                        $id = is_string($args['id'] ?? null) ? $args['id'] : '';
                        $product = $this->catalogService->product($id);

                        return $product instanceof AbstractProduct ? $this->mapProduct($product) : null;
                    },
                ],
                'currencies' => [
                    'type' => Type::nonNull($this->typeRegistry->listOfNonNull($this->typeRegistry->currency())),
                    'resolve' => function (): array {
                        return array_map(
                            static fn (Currency $currency): array => [
                                'label' => $currency->label,
                                'symbol' => $currency->symbol,
                            ],
                            $this->catalogService->currencies()
                        );
                    },
                ],
            ],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function mapProduct(AbstractProduct $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'inStock' => $product->inStock,
            'gallery' => $product->gallery,
            'description' => $product->description,
            'category' => $product->category,
            'brand' => $product->brand,
            'attributes' => array_map(
                static fn (AbstractAttributeSet $attributeSet): array => $attributeSet->toArray(),
                $product->attributes
            ),
            'prices' => array_map(
                static fn (Price $price): array => [
                    'amount' => $price->amount,
                    'currency' => [
                        'label' => $price->currency->label,
                        'symbol' => $price->currency->symbol,
                    ],
                ],
                $product->prices
            ),
        ];
    }
}
