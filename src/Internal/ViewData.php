<?php

namespace Nonetallt\LaravelResourceController\Internal;

class ViewData
{
    public function __construct(
        private string $name
    )
    {

    }

    public function getName() : string
    {
        return $this->name;
    }
}
