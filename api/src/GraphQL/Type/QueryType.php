<?php

declare(strict_types=1);

namespace App\GraphQL\Type;

use App\Infrastructure\Database\ConnectionFactory;
use Throwable;
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
                'dbHealth' => [
                    'type' => Type::string(),
                    'resolve' => static function (): string {
                        try {
                            $pdo = (new ConnectionFactory())->create();
                            $statement = $pdo->query('SELECT 1');
                            $result = $statement !== false ? $statement->fetchColumn() : false;

                            return $result !== false ? 'ok' : 'error';
                        } catch (Throwable) {
                            return 'error';
                        }
                    },
                ],
            ],
        ]);
    }
}
