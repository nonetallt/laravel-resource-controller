<?php

namespace Nonetallt\LaravelResourceBoiler\Console\Command;

use Nonetallt\LaravelResourceBoiler\ResourceControllerAction;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateResourceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:resource-boiler {resource} {--route-file=web}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate CRUD boilerplate for a new resource';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        dd('foo');
        $resource = $this->argument('resource');
        $resource = lcfirst($resource);
        $migration = "create_{$resource}s_table";
        $resource = ucfirst($resource);

        if($this->confirm("Create migration '$migration'?", true)) {
            $this->call('make:migration', [$migration]);
        }

        if($this->confirm("Create model '$resource'?", true)) {
            $this->call('make:model', [$resource]);
        }

        $requests = $this->createRequests($resource);
        $controller = "{$resource}Controller";

        if($this->confirm("Create controller '$controller'?")) {
            // create resource controller action
            $this->call('make:controller', [$controller, '--resource']);
        }

        $resourceSnakeCase = Str::snake($resource);
        $actions = implode(',', array_map(fn($action) => "'$action'", array_keys($requests)));
        $controllerClass = "App\\Http\\Controllers\\$controller";

        $routes = "Route::resource('$resourceSnakeCase', $controllerClass, ['only' => [$actions]])";
        $routeFile = $this->option('route-file');
        $routeFilePath = app_path("routes/$routeFile");

        file_put_contents($routeFilePath, file_get_contents($routeFilePath) . PHP_EOL . $routes);
    }

    /**
     * @return array<string> $requests
     */
    private function createRequests(string $resource) : array
    {
        $created = [];

        foreach (ResourceControllerAction::cases() as $case) {
            $action = ucfirst($case->value);
            $request = "$resource/$action{$resource}Request";
            $requestNamespace = 'App\\Http\\Requests\\';

            if($this->createRequest($request)) {
                $created[$case->value] = $requestNamespace . str_replace('/', '\\', $request);
            }
        }

        return $created;
    }

    private function createRequest(string $request)
    {
        if($this->confirm("Create request '$request'?")) {
            $this->call('make:request', [$request]);
            return true;
        }

        return false;
    }
}
