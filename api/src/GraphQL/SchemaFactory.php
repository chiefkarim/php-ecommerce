<?php

declare(strict_types=1);

namespace App\GraphQL;

use App\GraphQL\Type\MutationType;
use App\GraphQL\Type\QueryType;
use App\GraphQL\Type\TypeRegistry;
use App\Repository\MySql\MySqlCategoryRepository;
use App\Repository\MySql\MySqlCurrencyRepository;
use App\Repository\MySql\MySqlOrderRepository;
use App\Repository\MySql\MySqlProductRepository;
use App\Service\CatalogService;
use App\Service\OrderService;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;

final class SchemaFactory
{
    public function create(): Schema
    {
        $productRepository = new MySqlProductRepository();

        $catalogService = new CatalogService(
            new MySqlCategoryRepository(),
            $productRepository,
            new MySqlCurrencyRepository()
        );

        $orderService = new OrderService(
            $productRepository,
            new MySqlOrderRepository()
        );

        $typeRegistry = new TypeRegistry();
        $queryType = (new QueryType($catalogService, $typeRegistry))->build();
        $mutationType = (new MutationType($orderService))->build();

        return new Schema(
            (new SchemaConfig())
                ->setQuery($queryType)
                ->setMutation($mutationType)
        );
    }
}
