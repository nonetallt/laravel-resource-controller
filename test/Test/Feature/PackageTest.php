<?php

use Nonetallt\LaravelResourceController\ServiceProvider;

it('register the service provider in application container', function() {
    $provider = app()->resolveProvider(ServiceProvider::class);
    $this->assertInstanceof(ServiceProvider::class, $provider);
});
