<?php

namespace Nonetallt\LaravelResourceController;

class ResourceGeneratorConfigFactory
{
    private string $resourceName;
    private array $actions;

    public function __construct(string $resourceName)
    {
        $this->setResourceName($resourceName);
        $this->actions = [];
    }

    public function createConfig() : ResourceGeneratorConfig
    {
        // TODO
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
}
