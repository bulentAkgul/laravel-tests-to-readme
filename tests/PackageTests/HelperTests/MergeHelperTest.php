<?php

namespace Bakgul\LaravelTestsToReadme\Tests\PackageTests\HelperTests;

use Bakgul\LaravelTestsToReadme\Helpers\Merge;
use Bakgul\LaravelTestsToReadme\Tests\TestCase;

class MergeHelperTest extends TestCase
{
    /** @test */
    public function setter_will_set_bool_and_getters_will_return_bool_based_on_related_setting(): void
    {
        Merge::set(true);

        $this->assertTrue(Merge::true());
        $this->assertFalse(Merge::false());

        Merge::set(false);

        $this->assertTrue(Merge::false());
        $this->assertFalse(Merge::true());
    }
}
