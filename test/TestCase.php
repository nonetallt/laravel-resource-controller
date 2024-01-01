<?php

namespace Test;

use Nonetallt\LaravelResourceController\ServiceProvider;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as TestbenchTestCase;
use PainlessPHP\Filesystem\Filesystem;
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

    public function getProjectRootPath(string ...$appends) : string
    {
        $dir = Filesystem::findUpwards(__DIR__, 'composer.json')->getParentDirectory()->getPathname();
        return Filesystem::appendToPath($dir, ...$appends);
    }

    public function getTestInputPath(string ...$appends) : string
    {
        return $this->getProjectRootPath('test', 'input', ...$appends);
    }
}
