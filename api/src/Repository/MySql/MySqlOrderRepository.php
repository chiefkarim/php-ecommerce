<?php

declare(strict_types=1);

namespace App\Repository\MySql;

use App\Domain\Order\OrderItemData;
use App\Repository\OrderRepositoryInterface;
use Throwable;

final class MySqlOrderRepository extends AbstractMySqlRepository implements OrderRepositoryInterface
{
    public function createOrder(float $totalAmount, array $items): int
    {
        $pdo = $this->pdo();
        $pdo->beginTransaction();

        try {
            $insertOrder = $pdo->prepare('INSERT INTO orders (total_amount) VALUES (:total_amount)');
            $insertOrder->execute([
                'total_amount' => number_format($totalAmount, 2, '.', ''),
            ]);

            $orderId = (int) $pdo->lastInsertId();

            $insertOrderItem = $pdo->prepare(
                'INSERT INTO order_items (order_id, product_id, quantity, unit_amount)
                VALUES (:order_id, :product_id, :quantity, :unit_amount)'
            );
            $insertSelectedAttribute = $pdo->prepare(
                'INSERT INTO order_item_selected_attributes (order_item_id, attribute_id, item_id)
                VALUES (:order_item_id, :attribute_id, :item_id)'
            );

            foreach ($items as $item) {
                $insertOrderItem->execute([
                    'order_id' => $orderId,
                    'product_id' => $item->productId,
                    'quantity' => $item->quantity,
                    'unit_amount' => number_format($item->unitAmount, 2, '.', ''),
                ]);

                $orderItemId = (int) $pdo->lastInsertId();

                foreach ($item->selectedAttributes as $selectedAttribute) {
                    $insertSelectedAttribute->execute([
                        'order_item_id' => $orderItemId,
                        'attribute_id' => $selectedAttribute->attributeId,
                        'item_id' => $selectedAttribute->itemId,
                    ]);
                }
            }

            $pdo->commit();

            return $orderId;
        } catch (Throwable $throwable) {
            $pdo->rollBack();
            throw $throwable;
        }
    }
}
