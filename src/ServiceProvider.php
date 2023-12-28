<?php

namespace Nonetallt\LaravelResourceBoiler;

use Illuminate\Support\ServiceProvider as SupportServiceProvider;

class ServiceProvider extends SupportServiceProvider
{
    public function register() : void
    {
        $this->mergeConfigFrom(dirname(__DIR__) . PATH_SEPARATOR . 'resource-boiler.php', 'resource-boiler');
    }

    public function boot() : void
    {

    }
}
