<?php

declare(strict_types=1);

namespace App\GraphQL;

use App\GraphQL\Type\QueryType;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;

final class SchemaFactory
{
    public function create(): Schema
    {
        return new Schema(
            (new SchemaConfig())
                ->setQuery((new QueryType())->build())
        );
    }
}
