<?php

namespace Nonetallt\LaravelResourceBoiler;

use Illuminate\Support\ServiceProvider as SupportServiceProvider;
use Nonetallt\LaravelResourceBoiler\Console\Command\GenerateResourceCommand;

class ServiceProvider extends SupportServiceProvider
{
    public function register() : void
    {
        $this->mergeConfigFrom(dirname(__DIR__) . PATH_SEPARATOR . 'resource-boiler.php', 'resource-boiler');
    }

    public function boot() : void
    {
        if($this->app->runningInConsole()) {
            $this->commands([
                GenerateResourceCommand::class
            ]);
        }
    }
}
