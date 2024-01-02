<?php

namespace Nonetallt\LaravelResourceController\Interface;

interface CommandExecutor
{
    public function execute(string $command, array $args);
}
