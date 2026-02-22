<?php

declare(strict_types=1);

namespace Tests\Unit\Domain;

use App\Domain\Category\AllCategory;
use App\Domain\Category\CategoryTypeFactory;
use App\Domain\Category\StandardCategory;
use PHPUnit\Framework\TestCase;

final class CategoryTypeFactoryTest extends TestCase
{
    public function testCreatesAllCategoryForAllSlug(): void
    {
        $factory = new CategoryTypeFactory();
        $category = $factory->create(1, 'all', 'all');

        self::assertInstanceOf(AllCategory::class, $category);
        self::assertTrue($category->isAggregateCategory());
        self::assertNull($category->productFilterCategoryId());
    }

    public function testCreatesStandardCategoryForNonAllSlug(): void
    {
        $factory = new CategoryTypeFactory();
        $category = $factory->create(2, 'clothes', 'clothes');

        self::assertInstanceOf(StandardCategory::class, $category);
        self::assertFalse($category->isAggregateCategory());
        self::assertSame(2, $category->productFilterCategoryId());
    }
}
