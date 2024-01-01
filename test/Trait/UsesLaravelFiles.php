<?php

namespace Test\Trait;

use PainlessPHP\Filesystem\Directory;
use PainlessPHP\Filesystem\Filesystem;

trait UsesLaravelFiles
{
    protected function getProjectRoot() : string
    {
        return Filesystem::findUpwards(__FILE__, 'composer.json')->getPath();
    }

    protected function getInputDirectoryPath(string ...$append) : string
    {
        return Filesystem::appendToPath($this->getProjectRoot(), 'test', 'input', ...$append);
    }

    protected function getOutputDirectoryPath(string ...$append) : string
    {
        return Filesystem::appendToPath($this->getProjectRoot(), 'test', 'output', ...$append);
    }

    protected function initializeLaravelSkeleton()
    {
        // $input = Directory::createFromPath($this->getInputDirectoryPath('laravel-skeleton'));
        // $input->copy($this->getOutputDirectoryPath('laravel-skeleton'), recursive: true);
    }

    protected function cleanOutput()
    {
        $output = Directory::createFromPath($this->getOutputDirectoryPath());
        // $output->deleteContents(recursive: true, exclude: ['.keep']);
    }
}
