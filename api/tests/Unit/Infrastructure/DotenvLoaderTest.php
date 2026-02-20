<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure;

use App\Infrastructure\Config\DotenvLoader;
use PHPUnit\Framework\TestCase;

final class DotenvLoaderTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tempDir = sys_get_temp_dir() . '/dotenv-loader-' . uniqid('', true);
        mkdir($this->tempDir, 0777, true);
    }

    protected function tearDown(): void
    {
        $this->clearEnv('DATABASE_URL');
        $this->clearEnv('APP_NAME');
        $this->clearEnv('DOTENV_TEST_KEY');
        $this->clearEnv('DOTENV_TEST_OVERRIDE');

        foreach (glob($this->tempDir . '/*') ?: [] as $file) {
            @unlink($file);
        }
        @rmdir($this->tempDir);

        parent::tearDown();
    }

    public function testLoadsVariablesFromFile(): void
    {
        $file = $this->tempDir . '/api.env';
        file_put_contents($file, "APP_NAME=scandiweb\nDOTENV_TEST_KEY=test-value\n");

        DotenvLoader::boot([$file]);

        self::assertSame('scandiweb', $_ENV['APP_NAME'] ?? null);
        self::assertSame('test-value', getenv('DOTENV_TEST_KEY') ?: null);
    }

    public function testFallsBackToSecondFileWhenDatabaseUrlMissing(): void
    {
        $first = $this->tempDir . '/first.env';
        $second = $this->tempDir . '/second.env';

        file_put_contents($first, "APP_NAME=catalog\n");
        file_put_contents($second, "DATABASE_URL=mysql://user:pass@localhost:3306/scandiweb\n");

        DotenvLoader::boot([$first, $second]);

        self::assertSame('catalog', $_ENV['APP_NAME'] ?? null);
        self::assertSame('mysql://user:pass@localhost:3306/scandiweb', $_ENV['DATABASE_URL'] ?? null);
    }

    public function testDoesNotOverrideExistingEnvironmentVariables(): void
    {
        putenv('DOTENV_TEST_OVERRIDE=existing');
        $_ENV['DOTENV_TEST_OVERRIDE'] = 'existing';

        $file = $this->tempDir . '/override.env';
        file_put_contents($file, "DOTENV_TEST_OVERRIDE=new-value\n");

        DotenvLoader::boot([$file]);

        self::assertSame('existing', $_ENV['DOTENV_TEST_OVERRIDE'] ?? null);
        self::assertSame('existing', getenv('DOTENV_TEST_OVERRIDE') ?: null);
    }

    private function clearEnv(string $key): void
    {
        unset($_ENV[$key], $_SERVER[$key]);
        putenv($key);
    }
}
