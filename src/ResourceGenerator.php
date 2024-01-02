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
        $this->createRoutes($executor);
        $this->createViews($executor);
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

    public function createMigration(CommandExecutor $executor)
    {
        var_dump($this->config->getMigrationName());
        return $executor->execute('make:migration', ['name' => $this->config->getMigrationName()]);
    }

    private function createRequest(string $request, CommandExecutor $executor)
    {
        return $executor->execute('make:request', ['name' => $request]);
    }
}
