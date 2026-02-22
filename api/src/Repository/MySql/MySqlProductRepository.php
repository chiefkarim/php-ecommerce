<?php

declare(strict_types=1);

namespace App\Repository\MySql;

use App\Domain\Attribute\AttributeItem;
use App\Domain\Attribute\AttributeSetFactory;
use App\Domain\Currency\Currency;
use App\Domain\Price\Price;
use App\Domain\Product\AbstractProduct;
use App\Domain\Product\ProductTypeFactory;
use App\Repository\ProductRepositoryInterface;

final class MySqlProductRepository extends AbstractMySqlRepository implements ProductRepositoryInterface
{
    public function findByCategory(?int $categoryId): array
    {
        if ($categoryId === null) {
            $statement = $this->pdo()->query(
                'SELECT p.id, p.name, p.in_stock, p.description, p.category_id, p.brand, c.slug AS category_slug
                FROM products p
                INNER JOIN categories c ON c.id = p.category_id
                ORDER BY p.id ASC'
            );
        } else {
            $statement = $this->pdo()->prepare(
                'SELECT p.id, p.name, p.in_stock, p.description, p.category_id, p.brand, c.slug AS category_slug
                FROM products p
                INNER JOIN categories c ON c.id = p.category_id
                WHERE p.category_id = :category_id
                ORDER BY p.id ASC'
            );
            $statement->execute(['category_id' => $categoryId]);
        }

        $rows = $statement !== false ? $statement->fetchAll() : [];

        return array_map(fn (array $row): AbstractProduct => $this->hydrateProduct($row), $rows);
    }

    public function findById(string $productId): ?AbstractProduct
    {
        $statement = $this->pdo()->prepare(
            'SELECT p.id, p.name, p.in_stock, p.description, p.category_id, p.brand, c.slug AS category_slug
            FROM products p
            INNER JOIN categories c ON c.id = p.category_id
            WHERE p.id = :id
            LIMIT 1'
        );
        $statement->execute(['id' => $productId]);

        $row = $statement->fetch();
        if (!is_array($row)) {
            return null;
        }

        return $this->hydrateProduct($row);
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrateProduct(array $row): AbstractProduct
    {
        $productId = (string) $row['id'];

        $payload = [
            'id' => $productId,
            'name' => (string) $row['name'],
            'inStock' => (bool) $row['in_stock'],
            'description' => (string) $row['description'],
            'categoryId' => (int) $row['category_id'],
            'categorySlug' => (string) ($row['category_slug'] ?? ''),
            'brand' => (string) $row['brand'],
            'gallery' => $this->loadGallery($productId),
            'attributes' => $this->loadAttributes($productId),
            'prices' => $this->loadPrices($productId),
        ];

        return (new ProductTypeFactory())->create($payload);
    }

    /**
     * @return array<int, string>
     */
    private function loadGallery(string $productId): array
    {
        $statement = $this->pdo()->prepare(
            'SELECT image_url FROM product_galleries WHERE product_id = :product_id ORDER BY sort_order ASC'
        );
        $statement->execute(['product_id' => $productId]);
        $rows = $statement->fetchAll();

        return array_map(static fn (array $row): string => (string) $row['image_url'], $rows);
    }

    /**
     * @return array<int, Price>
     */
    private function loadPrices(string $productId): array
    {
        $statement = $this->pdo()->prepare(
            'SELECT p.amount, c.label, c.symbol
            FROM prices p
            INNER JOIN currencies c ON c.label = p.currency_label
            WHERE p.product_id = :product_id
            ORDER BY c.label ASC'
        );
        $statement->execute(['product_id' => $productId]);
        $rows = $statement->fetchAll();

        return array_map(
            static fn (array $row): Price => new Price(
                (float) $row['amount'],
                new Currency((string) $row['label'], (string) $row['symbol'])
            ),
            $rows
        );
    }

    /**
     * @return array<int, \App\Domain\Attribute\AbstractAttributeSet>
     */
    private function loadAttributes(string $productId): array
    {
        $statement = $this->pdo()->prepare(
            'SELECT id, external_id, name, type
            FROM attribute_sets
            WHERE product_id = :product_id
            ORDER BY sort_order ASC'
        );
        $statement->execute(['product_id' => $productId]);
        $sets = $statement->fetchAll();

        $factory = new AttributeSetFactory();

        return array_map(function (array $set) use ($factory): \App\Domain\Attribute\AbstractAttributeSet {
            $items = $this->loadAttributeItems((int) $set['id']);

            return $factory->create(
                (string) $set['external_id'],
                (string) $set['name'],
                (string) $set['type'],
                $items
            );
        }, $sets);
    }

    /**
     * @return array<int, AttributeItem>
     */
    private function loadAttributeItems(int $setId): array
    {
        $statement = $this->pdo()->prepare(
            'SELECT external_id, display_value, value
            FROM attribute_items
            WHERE attribute_set_id = :attribute_set_id
            ORDER BY sort_order ASC'
        );
        $statement->execute(['attribute_set_id' => $setId]);
        $rows = $statement->fetchAll();

        return array_map(
            static fn (array $row): AttributeItem => new AttributeItem(
                (string) $row['external_id'],
                (string) $row['display_value'],
                (string) $row['value']
            ),
            $rows
        );
    }
}
