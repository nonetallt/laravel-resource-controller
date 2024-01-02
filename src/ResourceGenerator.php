<?php

namespace Nonetallt\LaravelResourceController;

use Nonetallt\LaravelResourceController\Interface\CommandExecutor;
use Nonetallt\LaravelResourceController\Interface\ViewStubProvider;
use Nonetallt\LaravelResourceController\View\ResourceControllerViewStubProvider;
use PainlessPHP\Filesystem\Directory;
use PainlessPHP\Filesystem\File;

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
        $this->createViews($this->config->getViewStubProvider());
    }

    public function createMigration(CommandExecutor $executor)
    {
        return $executor->executeCommand('make:migration', ['name' => $this->config->getMigrationName()]);
    }

    public function createModel(CommandExecutor $executor)
    {
        return $executor->executeCommand('make:model', ['name' => $this->config->getModelName()]);
    }

    public function createRequests(CommandExecutor $executor) : array
    {
        $created = [];
        $resource = $this->config->getResourceName();
        $requestSubdir = $this->config->getRequestSubdirectory();
        $requestNamespace = $this->config->getRequestNamespace();

        foreach ($this->config->getActions() as $action) {
            $action = ucfirst($action);
            $requestName = "$action{$resource}Request";
            $request = "$requestSubdir/$requestName";

            $this->createRequest($request, $executor);
            $created[$action] = $requestNamespace . str_replace('/', '\\', $request);
        }

        return $created;
    }

    private function createRequest(string $request, CommandExecutor $executor)
    {
        return $executor->executeCommand('make:request', ['name' => $request]);
    }

    public function createController(CommandExecutor $executor)
    {
        // TODO resource controller
        return $executor->executeCommand('make:controller', ['name' => $this->config->getControllerName(), '--resource' => true]);
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

    public function createViews(ResourceControllerViewStubProvider $provider)
    {
        foreach($this->config->getActions() as $action) {

            $action = ResourceControllerAction::from($action);

            if($provider->hasAction($action)) {
                $this->createView($provider->getProvider($action));
            }
        }
    }

    private function createView(ViewStubProvider $provider)
    {
        $stub = new Stub($provider->getStubPath());

        if($this->config->shouldCreateViewsRecursively()) {
            $dir = (new File($provider->getOutputPath()))->getParentDirectory();
            $dir->create(recursive: true);
        }

        file_put_contents($provider->getOutputPath(), $stub->render($provider->getReplacements()));
    }

    public function getConfig() : ResourceGeneratorConfig
    {
        return $this->config;
    }
}
