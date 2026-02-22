<?php

declare(strict_types=1);

namespace App\Domain\Product;

use App\Domain\Attribute\AbstractAttributeSet;
use App\Domain\Order\OrderItemSelection;
use App\Domain\Price\Price;
use App\Infrastructure\Error\ValidationException;

abstract class AbstractProduct
{
    /**
     * @param array<int, string> $gallery
     * @param array<int, AbstractAttributeSet> $attributes
     * @param array<int, Price> $prices
     */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly bool $inStock,
        public readonly array $gallery,
        public readonly string $description,
        public readonly int $categoryId,
        public readonly string $brand,
        public readonly array $attributes,
        public readonly array $prices
    ) {
    }

    /**
     * @param mixed $selectedAttributesInput
     * @return array<int, OrderItemSelection>
     */
    final public function validateSelections(mixed $selectedAttributesInput): array
    {
        $providedSelections = $this->normalizeProvidedSelections($selectedAttributesInput);
        $allowedAttributeIds = $this->knownAttributeIds();

        foreach (array_keys($providedSelections) as $attributeId) {
            if (!in_array($attributeId, $allowedAttributeIds, true)) {
                throw new ValidationException(sprintf('Invalid selection for attribute: %s', $attributeId));
            }
        }

        $result = [];

        foreach ($this->attributes as $attributeSet) {
            $selectedItemId = $providedSelections[$attributeSet->id] ?? null;
            if (!is_string($selectedItemId) || $selectedItemId === '') {
                throw new ValidationException(sprintf('Missing selection for attribute: %s', $attributeSet->id));
            }

            if (!$this->attributeContainsItem($attributeSet, $selectedItemId)) {
                throw new ValidationException(sprintf('Invalid selection for attribute: %s', $attributeSet->id));
            }

            $result[] = new OrderItemSelection($attributeSet->id, $selectedItemId);
        }

        return $result;
    }

    /**
     * @param mixed $selectedAttributesInput
     * @return array<string, string>
     */
    abstract protected function normalizeProvidedSelections(mixed $selectedAttributesInput): array;

    abstract public function getTypeName(): string;

    /**
     * @return array<int, string>
     */
    private function knownAttributeIds(): array
    {
        return array_map(
            static fn (AbstractAttributeSet $attributeSet): string => $attributeSet->id,
            $this->attributes
        );
    }

    private function attributeContainsItem(AbstractAttributeSet $attributeSet, string $itemId): bool
    {
        foreach ($attributeSet->items as $attributeItem) {
            if ($attributeItem->id === $itemId) {
                return true;
            }
        }

        return false;
    }
}
