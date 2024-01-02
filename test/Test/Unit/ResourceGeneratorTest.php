<?php

use Nonetallt\LaravelResourceController\ResourceControllerAction;
use Nonetallt\LaravelResourceController\ResourceGenerator;
use Nonetallt\LaravelResourceController\ResourceGeneratorConfig;

describe('generateRequests', function() {

    test('request files do not exist before being generated', function () {
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

        $generator->generateRequests($this);

        foreach(ResourceControllerAction::cases() as $case) {
            $action = ucfirst($case->value);
            $this->assertFileExists(app_path("Http/Requests/$resource/$action{$resource}Request.php"));
        }
    });
});
