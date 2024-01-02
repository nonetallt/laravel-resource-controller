<?php

namespace Nonetallt\LaravelResourceController;

use Nonetallt\LaravelResourceController\Interface\CommandExecutor;

class ResourceGenerator
{
    public function __construct(private ResourceGeneratorConfig $config)
    {
    }

    public function generate(CommandExecutor $executor)
    {
        $this->createMigration($executor);
        $this->createModel($executor);
        $this->createRequests($executor);
        $this->createController($executor);
        $this->createControllerRoutes();
        $this->createViews();
    }

    public function createMigration(CommandExecutor $executor)
    {
        return $executor->execute('make:migration', ['name' => $this->config->getMigrationName()]);
    }

    public function createModel(CommandExecutor $executor)
    {
        return $executor->execute('make:model', ['name' => $this->config->getModelName()]);
    }

    public function createRequests(CommandExecutor $executor) : array
    {
        $created = [];
        $resource = $this->config->getResourceName();
        $requestSubdir = $this->config->getRequestSubdirectory();
        $requestNamespace = $this->config->getRequestNamespace();

        foreach (ResourceControllerAction::cases() as $case) {
            $action = ucfirst($case->value);
            $requestName = "$action{$resource}Request";
            $request = "$requestSubdir/$requestName";

            $this->createRequest($request, $executor);
            $created[$case->value] = $requestNamespace . str_replace('/', '\\', $request);
        }

        return $created;
    }

    private function createRequest(string $request, CommandExecutor $executor)
    {
        return $executor->execute('make:request', ['name' => $request]);
    }

    public function createController(CommandExecutor $executor)
    {
        // TODO resource controller
        return $executor->execute('make:controller', ['name' => $this->config->getControllerName(), '--resource' => true]);
    }

    public function createControllerRoutes()
    {
        $stub = new Stub($this->config->getResourceControllerRoutesStubPath());

        $routes = $stub->render([
            'route_resource_name' => $this->config->getRouteResourceName(),
            'controller_class' => $this->config->getControllerClass()
        ]);

        $routeFilePath = $this->config->getRouteFilePath();
        $oldContent = '';

        if(file_exists($routeFilePath)) {
            $oldContent = file_get_contents($routeFilePath);
        }

        file_put_contents($routeFilePath, $oldContent . PHP_EOL . $routes);
    }

    public function createViews()
    {
        $stub = new Stub($this->config->getViewStubPath());
        file_put_contents($this->config->getViewOutputPath(), $stub->render($this->getViewReplacemements()));
    }

    private function getViewReplacemements(ResourceControllerAction $action) : array
    {
        return [
            'view_name' => $this->config->getViewName($action),
            'view_prop_types' => implode(','),
            'view_prop_names' => implode(PHP_EOL),
            'js_imports' => implode(PHP_EOL)
        ];
    }

    public function getConfig() : ResourceGeneratorConfig
    {
        return $this->config;
    }
}
