<?php

namespace Nonetallt\LaravelResourceController\View;

class InertiaReactTypescriptProvider extends BaseViewStubProvider
{
    public function getStubPath() : string
    {
        return $this->getStubFilePath('view.inertia.react.ts');
    }

    public function getReplacements() : array
    {
        return [
            'view_name' => '',
            'view_prop_types' => $this->renderPropTypes(),
            'view_prop_names' => $this->renderPropNames(),
            'js_imports' => $this->renderJsImports()
        ];
    }

    private function renderPropTypes() : string
    {
        return '';
    }

    private function renderPropNames() : string
    {
        return '';
    }

    private function renderJsImports()  : string
    {
        return '';
    }
}
