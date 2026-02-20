<?php

declare(strict_types=1);

namespace App\Domain\Attribute;

final class SwatchAttributeSet extends AbstractAttributeSet
{
    public function getType(): string
    {
        return 'swatch';
    }
}
