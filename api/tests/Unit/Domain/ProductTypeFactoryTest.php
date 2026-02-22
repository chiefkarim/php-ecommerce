<?php

declare(strict_types=1);

namespace Tests\Unit\Domain;

use App\Domain\Attribute\AttributeItem;
use App\Domain\Attribute\TextAttributeSet;
use App\Domain\Currency\Currency;
use App\Domain\Price\Price;
use App\Domain\Product\ApparelProduct;
use App\Domain\Product\GenericProduct;
use App\Domain\Product\ProductTypeFactory;
use App\Domain\Product\TechProduct;
use PHPUnit\Framework\TestCase;

final class ProductTypeFactoryTest extends TestCase
{
    public function testCreatesApparelProductForClothesCategory(): void
    {
        $factory = new ProductTypeFactory();
        $product = $factory->create($this->payloadForCategory('clothes'));

        self::assertInstanceOf(ApparelProduct::class, $product);
        self::assertSame('apparel', $product->getTypeName());
    }

    public function testCreatesTechProductForTechCategory(): void
    {
        $factory = new ProductTypeFactory();
        $product = $factory->create($this->payloadForCategory('tech'));

        self::assertInstanceOf(TechProduct::class, $product);
        self::assertSame('tech', $product->getTypeName());
    }

    public function testFallsBackToGenericProductForUnknownCategory(): void
    {
        $factory = new ProductTypeFactory();
        $product = $factory->create($this->payloadForCategory('unknown'));

        self::assertInstanceOf(GenericProduct::class, $product);
        self::assertSame('generic', $product->getTypeName());
    }

    /**
     * @return array<string, mixed>
     */
    private function payloadForCategory(string $categorySlug): array
    {
        return [
            'id' => 'prod-1',
            'name' => 'Product Name',
            'inStock' => true,
            'gallery' => ['https://example.com/image.jpg'],
            'description' => 'Description',
            'categoryId' => 1,
            'categorySlug' => $categorySlug,
            'brand' => 'Brand',
            'attributes' => [
                new TextAttributeSet('size', 'Size', [
                    new AttributeItem('s', 'S', 'S'),
                ]),
            ],
            'prices' => [
                new Price(100.0, new Currency('USD', '$')),
            ],
        ];
    }
}
