<?php

namespace Nonetallt\LaravelResourceController\View;

use ArrayIterator;
use InvalidArgumentException;
use IteratorAggregate;
use Nonetallt\LaravelResourceController\Interface\ViewStubProvider;
use Nonetallt\LaravelResourceController\ResourceControllerAction;
use Traversable;

class ResourceControllerViewStubProvider implements IteratorAggregate
{
    /**
     * @var array<string,ViewStubProvider> $actions
     */
    private array $actions;

    public function __construct(array $actions)
    {
        $this->setActions($actions);
    }

    private function setActions(array $actions)
    {
        $this->actions = [];

        foreach ($actions as $key => $value) {
            $this->setAction($key, $value);
        }
    }

    private function setAction(string $key, ViewStubProvider $action)
    {
        if(! ResourceControllerAction::tryFrom($key)) {
            $msg = "Action array keys must be valid resource controller actions, '$key' given";
            throw new InvalidArgumentException($msg);
        }

        $this->actions[$key] = $action;
    }

    public function getProvider(ResourceControllerAction $action) : ViewStubProvider
    {
        if(! $this->hasAction($action)) {
            $msg = "No provider for action '{$action->value}'";
            throw new \Exception($msg);
        }

        return $this->actions[$action->value];
    }

    public function hasAction(ResourceControllerAction $action) : bool
    {
        return array_key_exists($action->value, $this->actions);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->actions);
    }
}
