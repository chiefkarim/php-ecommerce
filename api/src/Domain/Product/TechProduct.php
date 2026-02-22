<?php

declare(strict_types=1);

namespace App\Domain\Product;

use App\Infrastructure\Error\ValidationException;

final class TechProduct extends GenericProduct
{
    public function getTypeName(): string
    {
        return 'tech';
    }

    /**
     * @param mixed $selectedAttributesInput
     * @return array<string, string>
     */
    protected function normalizeProvidedSelections(mixed $selectedAttributesInput): array
    {
        $provided = [];

        if (!is_array($selectedAttributesInput)) {
            return $provided;
        }

        foreach ($selectedAttributesInput as $item) {
            if (!is_array($item)) {
                continue;
            }

            $attributeId = trim((string) ($item['attributeId'] ?? ''));
            $itemId = trim((string) ($item['itemId'] ?? ''));

            if ($attributeId === '' || $itemId === '') {
                continue;
            }

            if (array_key_exists($attributeId, $provided)) {
                throw new ValidationException(sprintf('Duplicate selection for attribute: %s', $attributeId));
            }

            $provided[$attributeId] = $itemId;
        }

        return $provided;
    }
}
