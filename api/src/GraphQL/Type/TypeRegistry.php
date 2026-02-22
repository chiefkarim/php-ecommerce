<?php

declare(strict_types=1);

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

final class TypeRegistry
{
    private ?ObjectType $categoryType = null;
    private ?ObjectType $currencyType = null;
    private ?ObjectType $priceType = null;
    private ?ObjectType $attributeItemType = null;
    private ?ObjectType $attributeSetType = null;
    private ?ObjectType $productType = null;

    public function category(): ObjectType
    {
        if ($this->categoryType instanceof ObjectType) {
            return $this->categoryType;
        }

        $this->categoryType = new ObjectType([
            'name' => 'Category',
            'fields' => [
                'id' => Type::nonNull(Type::id()),
                'name' => Type::nonNull(Type::string()),
                'slug' => Type::nonNull(Type::string()),
            ],
        ]);

        return $this->categoryType;
    }

    public function currency(): ObjectType
    {
        if ($this->currencyType instanceof ObjectType) {
            return $this->currencyType;
        }

        $this->currencyType = new ObjectType([
            'name' => 'Currency',
            'fields' => [
                'label' => Type::nonNull(Type::string()),
                'symbol' => Type::nonNull(Type::string()),
            ],
        ]);

        return $this->currencyType;
    }

    public function price(): ObjectType
    {
        if ($this->priceType instanceof ObjectType) {
            return $this->priceType;
        }

        $this->priceType = new ObjectType([
            'name' => 'Price',
            'fields' => fn (): array => [
                'amount' => Type::nonNull(Type::float()),
                'currency' => Type::nonNull($this->currency()),
            ],
        ]);

        return $this->priceType;
    }

    public function attributeItem(): ObjectType
    {
        if ($this->attributeItemType instanceof ObjectType) {
            return $this->attributeItemType;
        }

        $this->attributeItemType = new ObjectType([
            'name' => 'AttributeItem',
            'fields' => [
                'id' => Type::nonNull(Type::string()),
                'displayValue' => Type::nonNull(Type::string()),
                'value' => Type::nonNull(Type::string()),
            ],
        ]);

        return $this->attributeItemType;
    }

    public function attributeSet(): ObjectType
    {
        if ($this->attributeSetType instanceof ObjectType) {
            return $this->attributeSetType;
        }

        $this->attributeSetType = new ObjectType([
            'name' => 'AttributeSet',
            'fields' => fn (): array => [
                'id' => Type::nonNull(Type::string()),
                'name' => Type::nonNull(Type::string()),
                'type' => Type::nonNull(Type::string()),
                'items' => Type::nonNull(Type::listOf(Type::nonNull($this->attributeItem()))),
            ],
        ]);

        return $this->attributeSetType;
    }

    public function product(): ObjectType
    {
        if ($this->productType instanceof ObjectType) {
            return $this->productType;
        }

        $this->productType = new ObjectType([
            'name' => 'Product',
            'fields' => fn (): array => [
                'id' => Type::nonNull(Type::string()),
                'name' => Type::nonNull(Type::string()),
                'inStock' => Type::nonNull(Type::boolean()),
                'gallery' => Type::nonNull(Type::listOf(Type::nonNull(Type::string()))),
                'description' => Type::nonNull(Type::string()),
                'categoryId' => Type::nonNull(Type::id()),
                'brand' => Type::nonNull(Type::string()),
                'attributes' => Type::nonNull(Type::listOf(Type::nonNull($this->attributeSet()))),
                'prices' => Type::nonNull(Type::listOf(Type::nonNull($this->price()))),
            ],
        ]);

        return $this->productType;
    }

    public function listOfNonNull(ObjectType $type): ListOfType
    {
        return Type::listOf(Type::nonNull($type));
    }
}
