<?php

declare(strict_types=1);

namespace App\Repository\MySql;

use App\Domain\Currency\Currency;
use App\Repository\CurrencyRepositoryInterface;

final class MySqlCurrencyRepository extends AbstractMySqlRepository implements CurrencyRepositoryInterface
{
    public function findAll(): array
    {
        $statement = $this->pdo()->query('SELECT label, symbol FROM currencies ORDER BY label ASC');
        $rows = $statement !== false ? $statement->fetchAll() : [];

        return array_map(
            static fn (array $row): Currency => new Currency((string) $row['label'], (string) $row['symbol']),
            $rows
        );
    }
}
