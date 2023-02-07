<?php

namespace Bakgul\LaravelTestsToReadme\Tests;

use Tests\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->clearRay();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    protected function clearRay()
    {
        if (class_exists(\Spatie\LaravelRay\Ray::class)) ray()->clearAll();
    }
}
