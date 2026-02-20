<?php

declare(strict_types=1);

namespace App\Controller;

use App\GraphQL\SchemaFactory;

final class GraphQL
{
    /**
     * @param array<string, mixed> $vars
     */
    public static function handle(array $vars = []): string
    {
        unset($vars);

        $schemaFactory = new SchemaFactory();
        $controller = new GraphQLController($schemaFactory->create());

        return $controller->handle();
    }
}
