<?php

declare(strict_types=1);

use App\Infrastructure\Database\ConnectionFactory;

require_once __DIR__ . '/../vendor/autoload.php';

$pdo = (new ConnectionFactory())->create();
$schemaPath = __DIR__ . '/../database/schema.sql';
$dataPath = dirname(__DIR__, 2) . '/schema.json';

if (!file_exists($schemaPath)) {
    throw new RuntimeException('Schema file not found: ' . $schemaPath);
}

if (!file_exists($dataPath)) {
    throw new RuntimeException('Data file not found: ' . $dataPath);
}

$schemaSql = file_get_contents($schemaPath);
if (!is_string($schemaSql)) {
    throw new RuntimeException('Unable to read schema SQL');
}

$pdo->exec($schemaSql);

$json = file_get_contents($dataPath);
if (!is_string($json)) {
    throw new RuntimeException('Unable to read schema.json');
}

$payload = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
if (!is_array($payload) || !isset($payload['data']) || !is_array($payload['data'])) {
    throw new RuntimeException('Invalid schema.json shape');
}

$data = $payload['data'];
$categories = is_array($data['categories'] ?? null) ? $data['categories'] : [];
$products = is_array($data['products'] ?? null) ? $data['products'] : [];

$pdo->beginTransaction();

try {
    clearTables($pdo);

    $insertCategory = $pdo->prepare('INSERT INTO categories (name) VALUES (:name)');
    foreach ($categories as $category) {
        if (!is_array($category) || !is_string($category['name'] ?? null)) {
            continue;
        }

        $insertCategory->execute(['name' => $category['name']]);
    }

    $insertCurrency = $pdo->prepare('INSERT INTO currencies (label, symbol) VALUES (:label, :symbol) ON DUPLICATE KEY UPDATE symbol = VALUES(symbol)');
    $insertProduct = $pdo->prepare('INSERT INTO products (id, name, in_stock, description, category_name, brand) VALUES (:id, :name, :in_stock, :description, :category_name, :brand)');
    $insertGallery = $pdo->prepare('INSERT INTO product_galleries (product_id, image_url, sort_order) VALUES (:product_id, :image_url, :sort_order)');
    $insertAttributeSet = $pdo->prepare('INSERT INTO attribute_sets (product_id, external_id, name, type, sort_order) VALUES (:product_id, :external_id, :name, :type, :sort_order)');
    $insertAttributeItem = $pdo->prepare('INSERT INTO attribute_items (attribute_set_id, external_id, display_value, value, sort_order) VALUES (:attribute_set_id, :external_id, :display_value, :value, :sort_order)');
    $insertPrice = $pdo->prepare('INSERT INTO prices (product_id, currency_label, amount) VALUES (:product_id, :currency_label, :amount)');

    foreach ($products as $product) {
        if (!is_array($product) || !is_string($product['id'] ?? null)) {
            continue;
        }

        $productId = $product['id'];

        $insertProduct->execute([
            'id' => $productId,
            'name' => (string) ($product['name'] ?? ''),
            'in_stock' => (int) (!empty($product['inStock'])),
            'description' => (string) ($product['description'] ?? ''),
            'category_name' => (string) ($product['category'] ?? 'all'),
            'brand' => (string) ($product['brand'] ?? ''),
        ]);

        $gallery = is_array($product['gallery'] ?? null) ? $product['gallery'] : [];
        foreach ($gallery as $galleryIndex => $imageUrl) {
            if (!is_string($imageUrl)) {
                continue;
            }

            $insertGallery->execute([
                'product_id' => $productId,
                'image_url' => $imageUrl,
                'sort_order' => $galleryIndex,
            ]);
        }

        $attributes = is_array($product['attributes'] ?? null) ? $product['attributes'] : [];
        foreach ($attributes as $attributeIndex => $attributeSet) {
            if (!is_array($attributeSet)) {
                continue;
            }

            $insertAttributeSet->execute([
                'product_id' => $productId,
                'external_id' => (string) ($attributeSet['id'] ?? ''),
                'name' => (string) ($attributeSet['name'] ?? ''),
                'type' => (string) ($attributeSet['type'] ?? 'text'),
                'sort_order' => $attributeIndex,
            ]);

            $attributeSetId = (int) $pdo->lastInsertId();
            $items = is_array($attributeSet['items'] ?? null) ? $attributeSet['items'] : [];

            foreach ($items as $itemIndex => $item) {
                if (!is_array($item)) {
                    continue;
                }

                $insertAttributeItem->execute([
                    'attribute_set_id' => $attributeSetId,
                    'external_id' => (string) ($item['id'] ?? ''),
                    'display_value' => (string) ($item['displayValue'] ?? ''),
                    'value' => (string) ($item['value'] ?? ''),
                    'sort_order' => $itemIndex,
                ]);
            }
        }

        $prices = is_array($product['prices'] ?? null) ? $product['prices'] : [];
        foreach ($prices as $price) {
            if (!is_array($price)) {
                continue;
            }

            $currency = is_array($price['currency'] ?? null) ? $price['currency'] : [];
            $currencyLabel = (string) ($currency['label'] ?? 'USD');
            $currencySymbol = (string) ($currency['symbol'] ?? '$');

            $insertCurrency->execute([
                'label' => $currencyLabel,
                'symbol' => $currencySymbol,
            ]);

            $insertPrice->execute([
                'product_id' => $productId,
                'currency_label' => $currencyLabel,
                'amount' => number_format((float) ($price['amount'] ?? 0), 2, '.', ''),
            ]);
        }
    }

    $pdo->commit();
    fwrite(STDOUT, "Seed completed successfully.\n");
} catch (Throwable $throwable) {
    $pdo->rollBack();
    throw $throwable;
}

function clearTables(PDO $pdo): void
{
    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
    $pdo->exec('TRUNCATE TABLE order_item_selected_attributes');
    $pdo->exec('TRUNCATE TABLE order_items');
    $pdo->exec('TRUNCATE TABLE orders');
    $pdo->exec('TRUNCATE TABLE prices');
    $pdo->exec('TRUNCATE TABLE attribute_items');
    $pdo->exec('TRUNCATE TABLE attribute_sets');
    $pdo->exec('TRUNCATE TABLE product_galleries');
    $pdo->exec('TRUNCATE TABLE products');
    $pdo->exec('TRUNCATE TABLE currencies');
    $pdo->exec('TRUNCATE TABLE categories');
    $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
}
