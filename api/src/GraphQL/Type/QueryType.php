<?php

declare(strict_types=1);

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

final class QueryType
{
    public function build(): ObjectType
    {
        return new ObjectType([
            'name' => 'Query',
            'fields' => [
                'health' => [
                    'type' => Type::string(),
                    'resolve' => static fn (): string => 'ok',
                ],
            ],
        ]);
    }
}
