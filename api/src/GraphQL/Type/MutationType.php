<?php

declare(strict_types=1);

namespace App\GraphQL\Type;

use App\Service\OrderService;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

final class MutationType
{
    public function __construct(private readonly OrderService $orderService)
    {
    }

    public function build(): ObjectType
    {
        $selectedAttributeInput = new InputObjectType([
            'name' => 'SelectedAttributeInput',
            'fields' => [
                'attributeId' => Type::nonNull(Type::string()),
                'itemId' => Type::nonNull(Type::string()),
            ],
        ]);

        $orderItemInput = new InputObjectType([
            'name' => 'OrderItemInput',
            'fields' => [
                'productId' => Type::nonNull(Type::string()),
                'quantity' => Type::nonNull(Type::int()),
                'selectedAttributes' => Type::nonNull(Type::listOf(Type::nonNull($selectedAttributeInput))),
            ],
        ]);

        $placeOrderInput = new InputObjectType([
            'name' => 'PlaceOrderInput',
            'fields' => [
                'items' => Type::nonNull(Type::listOf(Type::nonNull($orderItemInput))),
                'totalAmount' => Type::nonNull(Type::float()),
            ],
        ]);

        $placeOrderPayload = new ObjectType([
            'name' => 'PlaceOrderPayload',
            'fields' => [
                'success' => Type::nonNull(Type::boolean()),
                'orderId' => Type::id(),
                'message' => Type::nonNull(Type::string()),
            ],
        ]);

        return new ObjectType([
            'name' => 'Mutation',
            'fields' => [
                'placeOrder' => [
                    'type' => Type::nonNull($placeOrderPayload),
                    'args' => [
                        'input' => Type::nonNull($placeOrderInput),
                    ],
                    'resolve' => function ($rootValue, array $args): array {
                        $input = is_array($args['input'] ?? null) ? $args['input'] : [];
                        $result = $this->orderService->placeOrder($input);

                        return [
                            'success' => $result->success,
                            'orderId' => $result->orderId,
                            'message' => $result->message,
                        ];
                    },
                ],
            ],
        ]);
    }
}
