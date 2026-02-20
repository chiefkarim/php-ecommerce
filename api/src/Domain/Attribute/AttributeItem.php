<?php

declare(strict_types=1);

namespace App\Domain\Attribute;

final class AttributeItem
{
    public function __construct(
        public readonly string $id,
        public readonly string $displayValue,
        public readonly string $value
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'displayValue' => $this->displayValue,
            'value' => $this->value,
        ];
    }
}
