<?php

declare(strict_types=1);

namespace App\Domain\Product;

class GenericProduct extends AbstractProduct
{
    public function getTypeName(): string
    {
        return 'generic';
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

            $provided[$attributeId] = $itemId;
        }

        return $provided;
    }
}
