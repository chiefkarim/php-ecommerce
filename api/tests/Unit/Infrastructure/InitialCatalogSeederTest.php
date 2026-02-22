<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure;

use App\Infrastructure\Database\Seed\InitialCatalogSeeder;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class InitialCatalogSeederTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tempDir = sys_get_temp_dir() . '/initial-seeder-' . uniqid('', true);
        mkdir($this->tempDir, 0777, true);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->tempDir . '/*') ?: [] as $file) {
            @unlink($file);
        }
        @rmdir($this->tempDir);

        parent::tearDown();
    }

    public function testLoadSeedDataParsesValidJson(): void
    {
        $file = $this->tempDir . '/schema.json';
        file_put_contents($file, json_encode([
            'data' => [
                'categories' => [
                    ['name' => 'all'],
                ],
                'products' => [
                    ['id' => 'prod-1'],
                ],
            ],
        ], JSON_THROW_ON_ERROR));

        $seeder = new InitialCatalogSeeder();
        $data = $this->invokeLoadSeedData($seeder, $file);

        self::assertSame([['name' => 'all']], $data['categories']);
        self::assertSame([['id' => 'prod-1']], $data['products']);
    }

    public function testLoadSeedDataThrowsOnInvalidShape(): void
    {
        $file = $this->tempDir . '/schema.json';
        file_put_contents($file, json_encode(['invalid' => true], JSON_THROW_ON_ERROR));

        $seeder = new InitialCatalogSeeder();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid schema.json shape');

        $this->invokeLoadSeedData($seeder, $file);
    }

    /**
     * @return array{categories: array<int, mixed>, products: array<int, mixed>}
     */
    private function invokeLoadSeedData(InitialCatalogSeeder $seeder, string $file): array
    {
        $method = new \ReflectionMethod($seeder, 'loadSeedData');
        $method->setAccessible(true);

        return $method->invoke($seeder, $file);
    }
}
