<?php

namespace Nonetallt\LaravelResourceController\Trait;

trait UsesStubs
{
    private function getLaravelApplicationStubPath() : string
    {
        return base_path('stubs');
    }

    private function getCustomStubPath(string $name) : string
    {
        return $this->getLaravelApplicationStubPath() . "/$name.stub";
    }

    protected function getPackageStubPath(string $name)
    {
        return __DIR__ . "/../../stub/$name.stub";
    }

    private function hasCustomStub(string $name) : bool
    {
        return file_exists($this->getCustomStubPath($name));
    }

    protected function getStubFilePath(string $name) : string
    {
        return $this->hasCustomStub($name) ? $this->getCustomStubPath($name) : $this->getPackageStubPath($name);
    }
}
