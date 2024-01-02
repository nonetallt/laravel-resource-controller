<?php

namespace Nonetallt\LaravelResourceController;

use Illuminate\Support\Str;
use Nonetallt\LaravelResourceController\Interface\ViewStubProvider;
use Nonetallt\LaravelResourceController\View\ResourceControllerViewStubProvider;

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
    private ResourceControllerViewStubProvider $viewProvider;

    public function __construct(
        string $resourceName,
        private ?string $requestSubdirectory = null,
        private bool $createViewsRecursively = false
    )
    {
        $this->setResourceName($resourceName);
        $this->actions = [];

        foreach (ResourceControllerAction::cases() as $case) {
            $this->addAction($case);
        }
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

    public function getMigrationTableName() : string
    {
        return Str::snake($this->getResourcePlural());
    }

    public function getMigrationName() : string
    {
        return "create_{$this->getMigrationTableName()}_table";
    }

    public function getResourcePlural() : string
    {
        return "{$this->resourceName}s";
    }

    public function getRequestSubdirectory() : string
    {
        if($this->requestSubdirectory) {
            return $this->requestSubdirectory;
        }

        return $this->getResourceName();
    }

    public function getRequestNamespace() : string
    {
        return 'App\\Http\\Requests\\';
    }

    public function getModelName() : string
    {
        return $this->resourceName;
    }

    public function getControllerName() : string
    {
        return "{$this->resourceName}Controller";
    }

    public function getControllerNamespace() : string
    {
        return 'App\\Http\\Controllers\\';
    }

    public function getControllerClass() : string
    {
        return $this->getControllerNamespace() . $this->getControllerName();
    }

    public function getRouteResourceName() : string
    {
        return Str::snake($this->resourceName);
    }

    public function getRouteFileName() : string
    {
        return 'web.php';
    }

    public function getRouteFilePath() : string
    {
        return base_path("routes/{$this->getRouteFileName()}");
    }

    public function getLaravelApplicationStubsPath() : string
    {
        return base_path('stubs');
    }

    public function getResourceControllerRoutesStubPath() : string
    {
        $stubFilename = 'route.resource-controller.stub';
        $customStubPath = $this->getLaravelApplicationStubsPath() . "/$stubFilename";

        if(file_exists($customStubPath)) {
            return $customStubPath;
        }

        return dirname(__DIR__) . "/stub/$stubFilename";
    }

    public function getViewStubProvider() : ResourceControllerViewStubProvider
    {
        return $this->viewProvider;
    }

    public function shouldCreateViewsRecursively() : bool
    {
        return $this->createViewsRecursively;
    }
}
