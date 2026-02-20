<?php

declare(strict_types=1);

namespace App\Domain\Attribute;

use RuntimeException;

final class AttributeSetFactory
{
    /**
     * @var array<string, callable(string, string, array): AbstractAttributeSet>
     */
    private array $builders;

    public function __construct()
    {
        $this->builders = [
            'text' => static fn (string $id, string $name, array $items): AbstractAttributeSet => new TextAttributeSet($id, $name, $items),
            'swatch' => static fn (string $id, string $name, array $items): AbstractAttributeSet => new SwatchAttributeSet($id, $name, $items),
        ];
    }

    /**
     * @param array<int, AttributeItem> $items
     */
    public function create(string $id, string $name, string $type, array $items): AbstractAttributeSet
    {
        $builder = $this->builders[$type] ?? $this->builders['text'] ?? null;
        if ($builder === null) {
            throw new RuntimeException('Attribute set builder is not configured');
        }

        return $builder($id, $name, $items);
    }
}
