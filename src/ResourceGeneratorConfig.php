<?php

namespace Nonetallt\LaravelResourceController;

class ResourceGeneratorConfig
{
    public const COMMANDS = [
        'migration'  => true,
        'model'      => true,
        'controller' => true,
        'requests'   => true
    ];

    private string $resourceName;
    private array $actions;

    public function __construct(string $resourceName)
    {
        $this->setResourceName($resourceName);
        $this->actions = [];
    }

    private function setResourceName(string $resourceName)
    {
        $this->resourceName = ucfirst($resourceName);
    }

    public function getResourceName() : string
    {
        return $this->resourceName;
    }

    public function addAction(ResourceControllerAction $action)
    {
        $this->actions[$action->value] = $action;
    }

    public function getActions() : array
    {
        return array_keys($this->actions);
    }

    public function getMigrationName() : string
    {
        return "create_{$this->getResourcePlural()}_table";
    }

    public function getResourcePlural() : string
    {
        return "{$this->resourceName}s";
    }

    public function getResourceControllerRoutesStub() : string
    {
        return file_get_contents();
    }

    public function getRouteFilePath() : string
    {
        $routeFilePath = app_path("routes/$routeFile");
    }

    public function getRequestSubdirectory() : string
    {
        return $this->getResourceName();
    }

    public function getRequestNamespace() : string
    {
        return 'App\\Http\\Requests\\';
    }
}
