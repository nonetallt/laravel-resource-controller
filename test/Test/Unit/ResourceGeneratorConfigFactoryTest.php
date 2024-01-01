<?php

use Nonetallt\LaravelResourceController\ResourceGeneratorConfigFactory;

it('sets resource name', function () {
    $factory = new ResourceGeneratorConfigFactory('foo');
    $this->assertSame('Foo', $factory->getResourceName());
});
