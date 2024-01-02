<?php

namespace Test\Trait;

use PainlessPHP\Filesystem\Directory;
use PainlessPHP\Filesystem\DirectoryIteratorConfig;
use PainlessPHP\Filesystem\Filesystem;
use PainlessPHP\Filesystem\FilesystemObject;

trait UsesLaravelFiles
{
    static protected function getProjectRootPath(string ...$appends) : string
    {
        $dir = Filesystem::findUpwards(__DIR__, 'composer.json')->getParentDirectory()->getPathname();
        return Filesystem::appendToPath($dir, ...$appends);
    }

    static protected function getTestInputDirectoryPath(string ...$append) : string
    {
        return Filesystem::appendToPath(self::getProjectRootPath('test', 'input', ...$append));
    }

    static protected function getTestOutputDirectoryPath(string ...$append) : string
    {
        return Filesystem::appendToPath(self::getProjectRootPath('test', 'output', ...$append));
    }

    protected function initializeLaravelSkeleton()
    {
        $laravelPath = base_path();
        $laravelDir = Directory::createFromPath($laravelPath);
        $laravelDir->delete(recursive: true);

        $input = Directory::createFromPath(self::getInputDirectoryPath('laravel-skeleton-template'));
        $input->copy(destination: $laravelPath, recursive: true);
    }

    protected function cleanOutput()
    {
        $outputDir = Directory::createFromPath(self::getTestOutputDirectoryPath());

        $outputDir->deleteContents(
            recursive: true,
            config: new DirectoryIteratorConfig(
                resultFilters: [
                    fn(FilesystemObject $file) => $file->getFilename() !== '.gitignore'
                ]
            )
        );
    }
}
