<?php

use Nonetallt\LaravelResourceController\ResourceGenerator;
use Nonetallt\LaravelResourceController\ResourceGeneratorConfig;

describe('generateRequests', function() {

    test('request files do not exist before being generated', function () {
        $this->assertDirectoryDoesNotExist(app_path('Http/Requests'));
    });

    it('creates files for every resource controller action request', function () {
        $generator = new ResourceGenerator(new ResourceGeneratorConfig('Foo'));
        // dd($generator->generateRequests($this));
    });
});
