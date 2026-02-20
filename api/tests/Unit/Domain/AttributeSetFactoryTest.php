<?php

declare(strict_types=1);

namespace Tests\Unit\Domain;

use App\Domain\Attribute\AttributeItem;
use App\Domain\Attribute\AttributeSetFactory;
use App\Domain\Attribute\SwatchAttributeSet;
use App\Domain\Attribute\TextAttributeSet;
use PHPUnit\Framework\TestCase;

final class AttributeSetFactoryTest extends TestCase
{
    public function testCreatesSwatchAttributeSet(): void
    {
        $factory = new AttributeSetFactory();
        $set = $factory->create('Color', 'Color', 'swatch', [new AttributeItem('Blue', 'Blue', '#0000ff')]);

        self::assertInstanceOf(SwatchAttributeSet::class, $set);
        self::assertSame('swatch', $set->getType());
    }

    public function testFallsBackToTextAttributeSet(): void
    {
        $factory = new AttributeSetFactory();
        $set = $factory->create('Size', 'Size', 'unknown-type', [new AttributeItem('S', 'Small', 'S')]);

        self::assertInstanceOf(TextAttributeSet::class, $set);
        self::assertSame('text', $set->getType());
    }
}
