<?php

use Nonetallt\LaravelResourceController\ResourceControllerAction;
use Nonetallt\LaravelResourceController\ResourceGenerator;
use Nonetallt\LaravelResourceController\ResourceGeneratorConfig;

describe('generateMigration', function() {

    test('migration file does not exist before being generated', function () {
        $this->assertNotContains('create_foos_table', $this->getMigrationNames());
    });

    it('creates migration file', function () {

        $resource = 'Foo';
        $generator = new ResourceGenerator(
            new ResourceGeneratorConfig(
                resourceName: $resource,
                requestSubdirectory: $resource
            )
        );

        $generator->createMigration($this);
        $this->assertContains('create_foos_table', $this->getMigrationNames());
    });
});

describe('generateModel', function() {

    test('model file does not exist before being generated', function () {
        $resource = 'Foo';
        $this->assertFileDoesNotExist(app_path("Models/$resource.php"));
    });

    it('creates model file', function () {
        $resource = 'Foo';
        $generator = new ResourceGenerator(
            new ResourceGeneratorConfig(
                resourceName: $resource
            )
        );
        $generator->createModel($this);
        $this->assertFileExists(app_path("Models/$resource.php"));
    });
});

describe('createRequests', function() {

    test('request files do not exist before being created', function () {
        $this->assertDirectoryDoesNotExist(app_path('Http/Requests'));
    });

    it('creates files for every resource controller action request', function () {

        $resource = 'Foo';
        $generator = new ResourceGenerator(
            new ResourceGeneratorConfig(
                resourceName: $resource
            )
        );

        $generator->createRequests($this);

        foreach(ResourceControllerAction::cases() as $case) {
            $action = ucfirst($case->value);
            $this->assertFileExists(app_path("Http/Requests/$resource/$action{$resource}Request.php"));
        }
    });
});

describe('generateController', function() {

    test('controllere file does not exist before being generated', function () {
        $resource = 'Foo';
        $this->assertFileDoesNotExist(app_path("Http/Controllers/$resource.php"));
    });

    it('creates model file', function () {
        $resource = 'Foo';
        $generator = new ResourceGenerator(
            new ResourceGeneratorConfig(
                resourceName: $resource
            )
        );
        $generator->createController($this);
        $this->assertFileExists(app_path("Http/Controllers/{$resource}Controller.php"));
    });
});
