<?php

namespace Test;

use Nonetallt\LaravelResourceBoiler\ServiceProvider;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as TestbenchTestCase;

class TestCase extends TestbenchTestCase
{
    use WithWorkbench;

    protected function setUp() : void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app) : array
    {
        return [
            ServiceProvider::class,
        ];
    }
}
