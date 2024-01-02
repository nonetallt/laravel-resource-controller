<?php

namespace Test\Trait;

use PainlessPHP\Filesystem\Directory;
use PainlessPHP\Filesystem\DirectoryIteratorConfig;
use PainlessPHP\Filesystem\Filesystem;
use PainlessPHP\Filesystem\FilesystemObject;

trait UsesLaravelFiles
{
    static protected function getLaravelApplicationPath() : string
    {
        return self::getTestOutputDirectoryPath('laravel-skeleton');
    }

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
        $input = Directory::createFromPath(self::getTestInputDirectoryPath('laravel-skeleton-template'));
        $input->copy(destination: self::getLaravelApplicationPath(), recursive: true);
    }

    protected function cleanOutput()
    {
        $outputDir = Directory::createFromPath(self::getTestOutputDirectoryPath());

        $outputDir->deleteContents(
            recursive: true,
            config: new DirectoryIteratorConfig(
                resultFilters: [
                    function(FilesystemObject $file) use($outputDir) {
                        $filepath = $file->getRelativePath($outputDir->getPathname());
                        return $filepath !== '.gitignore';
                    }
                ]
            )
        );
    }

    /**
     * Parse migration names from migration filenames by stripping dates and extension
     *
     */
    protected function getMigrationNames() : array
    {
        $migrationDir = Directory::createFromPath(database_path('migrations'));

        return array_map(function(FilesystemObject $obj) {
            return basename(implode('_', array_slice(explode('_', $obj->getFilename()), 4)), '.php');
        }, $migrationDir->getContents(recursive: true));
    }
}
