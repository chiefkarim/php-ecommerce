<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Migration;

use PDO;

final class InitialSchemaMigration
{
    public function up(PDO $pdo): void
    {
        $pdo->exec('CREATE TABLE IF NOT EXISTS categories (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        $pdo->exec('CREATE TABLE IF NOT EXISTS products (
            id VARCHAR(255) PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            in_stock TINYINT(1) NOT NULL,
            description MEDIUMTEXT NOT NULL,
            category_name VARCHAR(255) NOT NULL,
            brand VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_products_category_name FOREIGN KEY (category_name) REFERENCES categories(name)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        $pdo->exec('CREATE TABLE IF NOT EXISTS product_galleries (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            product_id VARCHAR(255) NOT NULL,
            image_url TEXT NOT NULL,
            sort_order INT UNSIGNED NOT NULL,
            CONSTRAINT fk_product_galleries_product_id FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
            INDEX idx_product_galleries_product_id (product_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        $pdo->exec('CREATE TABLE IF NOT EXISTS attribute_sets (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            product_id VARCHAR(255) NOT NULL,
            external_id VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL,
            type VARCHAR(50) NOT NULL,
            sort_order INT UNSIGNED NOT NULL,
            CONSTRAINT fk_attribute_sets_product_id FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
            INDEX idx_attribute_sets_product_id (product_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        $pdo->exec('CREATE TABLE IF NOT EXISTS attribute_items (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            attribute_set_id BIGINT UNSIGNED NOT NULL,
            external_id VARCHAR(255) NOT NULL,
            display_value VARCHAR(255) NOT NULL,
            value VARCHAR(255) NOT NULL,
            sort_order INT UNSIGNED NOT NULL,
            CONSTRAINT fk_attribute_items_attribute_set_id FOREIGN KEY (attribute_set_id) REFERENCES attribute_sets(id) ON DELETE CASCADE,
            INDEX idx_attribute_items_attribute_set_id (attribute_set_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        $pdo->exec('CREATE TABLE IF NOT EXISTS currencies (
            label VARCHAR(10) PRIMARY KEY,
            symbol VARCHAR(20) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        $pdo->exec('CREATE TABLE IF NOT EXISTS prices (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            product_id VARCHAR(255) NOT NULL,
            currency_label VARCHAR(10) NOT NULL,
            amount DECIMAL(12, 2) NOT NULL,
            CONSTRAINT fk_prices_product_id FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
            CONSTRAINT fk_prices_currency_label FOREIGN KEY (currency_label) REFERENCES currencies(label),
            UNIQUE KEY uniq_prices_product_currency (product_id, currency_label)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        $pdo->exec('CREATE TABLE IF NOT EXISTS orders (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            total_amount DECIMAL(12, 2) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        $pdo->exec('CREATE TABLE IF NOT EXISTS order_items (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            order_id BIGINT UNSIGNED NOT NULL,
            product_id VARCHAR(255) NOT NULL,
            quantity INT UNSIGNED NOT NULL,
            unit_amount DECIMAL(12, 2) NOT NULL,
            CONSTRAINT fk_order_items_order_id FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
            INDEX idx_order_items_order_id (order_id),
            INDEX idx_order_items_product_id (product_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        $pdo->exec('CREATE TABLE IF NOT EXISTS order_item_selected_attributes (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            order_item_id BIGINT UNSIGNED NOT NULL,
            attribute_id VARCHAR(255) NOT NULL,
            item_id VARCHAR(255) NOT NULL,
            CONSTRAINT fk_order_item_selected_attributes_order_item_id FOREIGN KEY (order_item_id) REFERENCES order_items(id) ON DELETE CASCADE,
            INDEX idx_order_item_selected_attributes_order_item_id (order_item_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
    }

    public function down(PDO $pdo): void
    {
        $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
        $pdo->exec('DROP TABLE IF EXISTS order_item_selected_attributes');
        $pdo->exec('DROP TABLE IF EXISTS order_items');
        $pdo->exec('DROP TABLE IF EXISTS orders');
        $pdo->exec('DROP TABLE IF EXISTS prices');
        $pdo->exec('DROP TABLE IF EXISTS currencies');
        $pdo->exec('DROP TABLE IF EXISTS attribute_items');
        $pdo->exec('DROP TABLE IF EXISTS attribute_sets');
        $pdo->exec('DROP TABLE IF EXISTS product_galleries');
        $pdo->exec('DROP TABLE IF EXISTS products');
        $pdo->exec('DROP TABLE IF EXISTS categories');
        $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
    }
}
