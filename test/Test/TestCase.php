<?php

namespace Test;

use Nonetallt\LaravelResourceController\ServiceProvider;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as TestbenchTestCase;
use Test\Trait\UsesLaravelFiles;

class TestCase extends TestbenchTestCase
{
    use WithWorkbench, UsesLaravelFiles;

    protected function setUp() : void
    {
        parent::setUp();
        $this->initializeLaravelSkeleton();
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
        return self::getTestInputDirectoryPath('laravel-skeleton');
    }
}
