<?php

namespace Nonetallt\LaravelResourceController;

class Stub
{
    public function __construct(private string $path)
    {
    }

    public function render(array $replacements) : string
    {
        $result = $this->getContents();

        foreach ($replacements as $key => $value) {
            $result = str_replace("{{ $key }}", $value, $result);
        }

        return $result;
    }

    public function getContents() : string
    {
        return file_get_contents($this->path);
    }

    public function getPathname() : string
    {
        return $this->path;
    }
}
