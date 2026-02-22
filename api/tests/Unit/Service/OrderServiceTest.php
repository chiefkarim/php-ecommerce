<?php

declare(strict_types=1);

namespace Tests\Unit\Service;

use App\Domain\Attribute\AttributeItem;
use App\Domain\Attribute\TextAttributeSet;
use App\Domain\Currency\Currency;
use App\Domain\Price\Price;
use App\Domain\Product\CatalogProduct;
use App\Repository\OrderRepositoryInterface;
use App\Repository\ProductRepositoryInterface;
use App\Service\OrderService;
use PHPUnit\Framework\TestCase;

final class OrderServiceTest extends TestCase
{
    public function testRejectsMissingAttributeSelection(): void
    {
        $productRepository = new class implements ProductRepositoryInterface {
            public function findByCategory(?int $categoryId): array
            {
                return [];
            }

            public function findById(string $productId): ?\App\Domain\Product\AbstractProduct
            {
                return new CatalogProduct(
                    id: 'ps-5',
                    name: 'PlayStation 5',
                    inStock: true,
                    gallery: [],
                    description: '',
                    categoryId: 1,
                    brand: 'Sony',
                    attributes: [
                        new TextAttributeSet('Capacity', 'Capacity', [
                            new AttributeItem('1T', '1T', '1T'),
                        ]),
                    ],
                    prices: [new Price(100.00, new Currency('USD', '$'))]
                );
            }
        };

        $orderRepository = new class implements OrderRepositoryInterface {
            public function createOrder(float $totalAmount, array $items): int
            {
                return 1;
            }
        };

        $service = new OrderService($productRepository, $orderRepository);
        $result = $service->placeOrder([
            'items' => [
                [
                    'productId' => 'ps-5',
                    'quantity' => 1,
                    'selectedAttributes' => [],
                ],
            ],
            'totalAmount' => 100.00,
        ]);

        self::assertFalse($result->success);
        self::assertSame('Missing selection for attribute: Capacity', $result->message);
    }

    public function testCreatesOrderWhenInputIsValid(): void
    {
        $productRepository = new class implements ProductRepositoryInterface {
            public function findByCategory(?int $categoryId): array
            {
                return [];
            }

            public function findById(string $productId): ?\App\Domain\Product\AbstractProduct
            {
                return new CatalogProduct(
                    id: 'ps-5',
                    name: 'PlayStation 5',
                    inStock: true,
                    gallery: [],
                    description: '',
                    categoryId: 1,
                    brand: 'Sony',
                    attributes: [
                        new TextAttributeSet('Capacity', 'Capacity', [
                            new AttributeItem('1T', '1T', '1T'),
                        ]),
                    ],
                    prices: [new Price(100.00, new Currency('USD', '$'))]
                );
            }
        };

        $orderRepository = new class implements OrderRepositoryInterface {
            public function createOrder(float $totalAmount, array $items): int
            {
                return 55;
            }
        };

        $service = new OrderService($productRepository, $orderRepository);
        $result = $service->placeOrder([
            'items' => [
                [
                    'productId' => 'ps-5',
                    'quantity' => 2,
                    'selectedAttributes' => [
                        [
                            'attributeId' => 'Capacity',
                            'itemId' => '1T',
                        ],
                    ],
                ],
            ],
            'totalAmount' => 200.00,
        ]);

        self::assertTrue($result->success);
        self::assertSame(55, $result->orderId);
    }
}
