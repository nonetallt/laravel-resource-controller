<?php

use Nonetallt\LaravelResourceController\Internal\ViewData;
use Nonetallt\LaravelResourceController\ResourceControllerAction;
use Nonetallt\LaravelResourceController\ResourceGenerator;
use Nonetallt\LaravelResourceController\ResourceGeneratorConfig;
use Nonetallt\LaravelResourceController\View\InertiaReactTypescriptProvider;
use Nonetallt\LaravelResourceController\View\ResourceControllerViewStubProvider;

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

describe('createController', function() {

    test('controller file does not exist before being generated', function () {
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

describe('createControllerRoutes', function() {

    test('routes file does not exist before being generated', function () {
        $this->assertFileDoesNotExist(base_path('routes/web.php'));
    });

    it('creates model file', function () {
        $resource = 'Foo';
        $generator = new ResourceGenerator(
            new ResourceGeneratorConfig(
                resourceName: $resource
            )
        );
        $generator->createControllerRoutes();
        $routeFilePath = base_path('routes/web.php');
        $this->assertFileExists($routeFilePath);
        $this->assertSame(file_get_contents(self::getTestInputDirectoryPath('expectation/FooControllerRoutes.php')), file_get_contents($routeFilePath));
    });
});

describe('createViews', function() {

    describe('inertia.react.ts view', function() {

        test('view file does not exist before being generated', function () {
            $this->assertFileDoesNotExist(resource_path('js/Pages/Foo/FooIndex.tsx'));
        });

        it('creates view file', function () {
            $resource = 'Foo';
            $generator = new ResourceGenerator(
                new ResourceGeneratorConfig(
                    resourceName: $resource,
                    createViewsRecursively: true
                )
            );

            $providers = collect(ResourceControllerAction::cases())->mapWithKeys(function(ResourceControllerAction $action) use($resource) {
                $actionName = ucfirst($action->value);
                $viewName = "$resource$actionName";
                $provider = new InertiaReactTypescriptProvider(
                    outputPath: resource_path("js/Pages/$resource/$viewName.tsx"),
                    viewData: new ViewData(name: $viewName)
                );
                return [$action->value => $provider];
            })->toArray();

            $generator->createViews(new ResourceControllerViewStubProvider($providers));
            $viewPath = resource_path('js/Pages/Foo/FooIndex.tsx');
            $this->assertFileExists($viewPath);
            $this->assertSame(file_get_contents(self::getTestInputDirectoryPath('expectation/FooIndex.tsx')), file_get_contents($viewPath));
        });
    });
});
