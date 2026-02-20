<?php

declare(strict_types=1);

namespace App\GraphQL;

use App\GraphQL\Type\QueryType;
use App\GraphQL\Type\TypeRegistry;
use App\Repository\MySql\MySqlCategoryRepository;
use App\Repository\MySql\MySqlCurrencyRepository;
use App\Repository\MySql\MySqlProductRepository;
use App\Service\CatalogService;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;

final class SchemaFactory
{
    public function create(): Schema
    {
        $catalogService = new CatalogService(
            new MySqlCategoryRepository(),
            new MySqlProductRepository(),
            new MySqlCurrencyRepository()
        );

        $typeRegistry = new TypeRegistry();
        $queryType = (new QueryType($catalogService, $typeRegistry))->build();

        return new Schema(
            (new SchemaConfig())
                ->setQuery($queryType)
        );
    }
}
