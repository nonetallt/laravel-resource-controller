<?php

namespace Nonetallt\LaravelResourceController\Interface;

interface ViewStubProvider
{
    public function getStubPath() : string;

    public function getOutputPath() : string;

    /**
     * @return array<string>
     */
    public function getReplacements() : array;
}
