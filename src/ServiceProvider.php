<?php

namespace Nonetallt\LaravelResourceController;

use Illuminate\Support\ServiceProvider as SupportServiceProvider;
use Nonetallt\LaravelResourceController\Console\Command\GenerateResourceCommand;

class ServiceProvider extends SupportServiceProvider
{
    public const PACKAGE_NAME = 'resource-controller';
    private const CONFIG_PATH = __DIR__ . '/../config/' . self::PACKAGE_NAME . '.php';

    public function register() : void
    {
        $this->mergeConfigFrom(self::CONFIG_PATH, self::PACKAGE_NAME);
    }

    public function boot() : void
    {
        if($this->app->runningInConsole()) {
            $this->commands([
                GenerateResourceCommand::class
            ]);
        }

        $this->publishes([
            self::CONFIG_PATH => config_path(self::PACKAGE_NAME . '.php')
        ], 'config');

        $this->publishes([
            __DIR__ . '/../stubs/' => app_path('stubs')
        ], 'stubs');
    }
}
