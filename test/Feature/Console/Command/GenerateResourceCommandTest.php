<?php

use Nonetallt\LaravelResourceBoiler\Console\Command\GenerateResourceCommand;
use Nonetallt\LaravelResourceBoiler\ResourceControllerAction;
use Test\TestCase;

test('example', function () {
    /** @var TestCase $this */

    $artisan = $this->artisan(GenerateResourceCommand::class, ['resource' => 'Foo'])
    ->expectsConfirmation("Create migration 'create_foos_table'?")
    ->expectsConfirmation("Create model 'Foo'?");

    foreach(ResourceControllerAction::cases() as $case) {
        $action = ucfirst($case->value);
        $artisan = $artisan->expectsConfirmation("Create request 'Foo/{$action}FooRequest'?");
    }

    $artisan->expectsConfirmation("Create controller 'FooController'?");

    // var_dump('foo');
});
