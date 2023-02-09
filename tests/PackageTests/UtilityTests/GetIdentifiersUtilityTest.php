<?php

namespace Bakgul\LaravelTestsToReadme\Tests\PackageTests\UtilityTests;

use Bakgul\LaravelTestsToReadme\Tests\TestCase;
use Bakgul\LaravelTestsToReadme\Utilities\GetIdentifiers;

class GetIdentifiersUtilityTest extends TestCase
{
    /** @test */
    public function it_will_return_the_identifiers_as_assoc_array(): void
    {
        $this->assertEquals([
            'class' => '## ',
            'method' => '### ',
            'code' => '```php'
        ], GetIdentifiers::_());
    }
}
