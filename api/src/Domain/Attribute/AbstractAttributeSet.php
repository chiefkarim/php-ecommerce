<?php

declare(strict_types=1);

namespace App\Domain\Attribute;

abstract class AbstractAttributeSet
{
    /**
     * @param array<int, AttributeItem> $items
     */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly array $items
    ) {
    }

    abstract public function getType(): string;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->getType(),
            'items' => array_map(
                static fn (AttributeItem $item): array => $item->toArray(),
                $this->items
            ),
        ];
    }
}
