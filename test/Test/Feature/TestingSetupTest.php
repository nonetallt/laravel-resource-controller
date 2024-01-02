<?php

use Test\TestCase;

it('uses the TestCase parent class', function () {
    $this->assertInstanceof(TestCase::class, $this);
});

it('sets the correct laravel application path', function() {
    $this->assertSame(self::getTestOutputDirectoryPath('laravel-skeleton', 'app'), app_path());
});
