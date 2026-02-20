<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure;

use App\Infrastructure\Database\DatabaseUrl;
use App\Infrastructure\Error\ConfigurationException;
use PHPUnit\Framework\TestCase;

final class DatabaseUrlTest extends TestCase
{
    public function testParsesMySqlDatabaseUrl(): void
    {
        $url = DatabaseUrl::fromString('mysql://user:pass@db.example.com:3307/scandiweb');

        self::assertSame('db.example.com', $url->host);
        self::assertSame(3307, $url->port);
        self::assertSame('scandiweb', $url->database);
        self::assertSame('user', $url->username);
        self::assertSame('pass', $url->password);
    }

    public function testThrowsOnInvalidScheme(): void
    {
        $this->expectException(ConfigurationException::class);

        DatabaseUrl::fromString('https://example.com');
    }
}
