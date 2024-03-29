<?php

namespace Test;

use Nonetallt\LaravelResourceController\Interface\CommandExecutor;
use Nonetallt\LaravelResourceController\ServiceProvider;
use Orchestra\Testbench\TestCase as TestbenchTestCase;
use Test\Trait\UsesLaravelFiles;

class TestCase extends TestbenchTestCase implements CommandExecutor
{
    use UsesLaravelFiles;

    protected function setUp() : void
    {
        $this->cleanOutput();
        $this->initializeLaravelSkeleton();
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->cleanOutput();
    }

    protected function getPackageProviders($app) : array
    {
        return [
            ServiceProvider::class,
        ];
    }

    public static function applicationBasePath()
    {
        return self::getLaravelApplicationPath();
    }

    public function executeCommand(string $command, array $args)
    {
        return $this->artisan($command, $args);
    }
}
