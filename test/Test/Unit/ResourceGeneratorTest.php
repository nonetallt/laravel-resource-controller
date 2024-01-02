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

describe('createRequests', function() {

    test('request files do not exist before being created', function () {
        $this->assertDirectoryDoesNotExist(app_path('Http/Requests'));
    });

    it('creates files for every resource controller action request', function () {

        $resource = 'Foo';
        $generator = new ResourceGenerator(
            new ResourceGeneratorConfig(
                resourceName: $resource,
                requestSubdirectory: $resource
            )
        );

        $generator->createRequests($this);

        foreach(ResourceControllerAction::cases() as $case) {
            $action = ucfirst($case->value);
            $this->assertFileExists(app_path("Http/Requests/$resource/$action{$resource}Request.php"));
        }
    });
});
