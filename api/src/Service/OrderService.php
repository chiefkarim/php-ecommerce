<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Attribute\AbstractAttributeSet;
use App\Domain\Order\OrderItemData;
use App\Domain\Order\OrderItemSelection;
use App\Domain\Order\PlaceOrderResult;
use App\Domain\Product\AbstractProduct;
use App\Infrastructure\Error\ValidationException;
use App\Repository\OrderRepositoryInterface;
use App\Repository\ProductRepositoryInterface;
use Throwable;

final class OrderService
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly OrderRepositoryInterface $orderRepository
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function placeOrder(array $input): PlaceOrderResult
    {
        try {
            $itemsInput = $input['items'] ?? null;
            if (!is_array($itemsInput) || $itemsInput === []) {
                throw new ValidationException('Cart is empty');
            }

            $expectedTotalAmount = isset($input['totalAmount']) ? (float) $input['totalAmount'] : 0.0;
            $orderItems = [];
            $computedTotalAmount = 0.0;

            foreach ($itemsInput as $itemInput) {
                if (!is_array($itemInput)) {
                    throw new ValidationException('Invalid order item payload');
                }

                $productId = (string) ($itemInput['productId'] ?? '');
                $quantity = (int) ($itemInput['quantity'] ?? 0);
                $selectedAttributesInput = $itemInput['selectedAttributes'] ?? [];

                if ($productId === '') {
                    throw new ValidationException('Product id is required');
                }

                if ($quantity < 1) {
                    throw new ValidationException('Quantity must be at least 1');
                }

                $product = $this->productRepository->findById($productId);
                if (!$product instanceof AbstractProduct) {
                    throw new ValidationException(sprintf('Product not found: %s', $productId));
                }

                $selectedAttributes = $this->validateAndMapSelectedAttributes($product, $selectedAttributesInput);
                $unitAmount = $this->resolveUnitAmount($product);
                $computedTotalAmount += $unitAmount * $quantity;

                $orderItems[] = new OrderItemData(
                    productId: $productId,
                    quantity: $quantity,
                    unitAmount: $unitAmount,
                    selectedAttributes: $selectedAttributes
                );
            }

            $computedTotalAmount = round($computedTotalAmount, 2);
            if (abs($computedTotalAmount - $expectedTotalAmount) > 0.01) {
                throw new ValidationException('Cart total does not match server total');
            }

            $orderId = $this->orderRepository->createOrder($computedTotalAmount, $orderItems);

            return new PlaceOrderResult(true, $orderId, 'Order placed successfully');
        } catch (ValidationException $exception) {
            return new PlaceOrderResult(false, null, $exception->getMessage());
        } catch (Throwable) {
            return new PlaceOrderResult(false, null, 'Failed to place order');
        }
    }

    /**
     * @param mixed $selectedAttributesInput
     * @return array<int, OrderItemSelection>
     */
    private function validateAndMapSelectedAttributes(AbstractProduct $product, mixed $selectedAttributesInput): array
    {
        $provided = [];

        if (is_array($selectedAttributesInput)) {
            foreach ($selectedAttributesInput as $item) {
                if (!is_array($item)) {
                    continue;
                }

                $attributeId = (string) ($item['attributeId'] ?? '');
                $itemId = (string) ($item['itemId'] ?? '');

                if ($attributeId !== '' && $itemId !== '') {
                    $provided[$attributeId] = $itemId;
                }
            }
        }

        $result = [];

        foreach ($product->attributes as $attributeSet) {
            $selectedItemId = $provided[$attributeSet->id] ?? null;
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

    private function resolveUnitAmount(AbstractProduct $product): float
    {
        if ($product->prices === []) {
            throw new ValidationException('Product price is missing');
        }

        return round((float) $product->prices[0]->amount, 2);
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
