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
        $this->generateMigration($executor);
        $this->generateModel($executor);
        $this->generateRequests($executor);
        $this->generateController($executor);
        $this->generateRoutes($executor);
        $this->generateViews($executor);
    }

    public function generateRequests(CommandExecutor $executor) : array
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
}
