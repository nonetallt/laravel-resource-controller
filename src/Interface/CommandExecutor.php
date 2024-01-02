<?php

namespace Nonetallt\LaravelResourceController\Interface;

interface CommandExecutor
{
    public function executeCommand(string $command, array $args);
}
