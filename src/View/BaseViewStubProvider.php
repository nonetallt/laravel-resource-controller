<?php

namespace Nonetallt\LaravelResourceController\View;

use Nonetallt\LaravelResourceController\Interface\ViewStubProvider;
use Nonetallt\LaravelResourceController\Internal\ViewData;
use Nonetallt\LaravelResourceController\Trait\UsesStubs;

abstract class BaseViewStubProvider implements ViewStubProvider
{
    use UsesStubs;

    public function __construct(
        private string $outputPath,
        protected ViewData $viewData
    )
    {
    }

    public function getOutputPath() : string
    {
        return $this->outputPath;
    }
}
