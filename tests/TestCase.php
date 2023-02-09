<?php

namespace Bakgul\LaravelTestsToReadme\Tests;

use Bakgul\LaravelDumpServer\Concerns\HasDumper;
use Tests\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use HasDumper;

    public function setUp(): void
    {
        parent::setUp();

        $this->resetDumper();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }
}
