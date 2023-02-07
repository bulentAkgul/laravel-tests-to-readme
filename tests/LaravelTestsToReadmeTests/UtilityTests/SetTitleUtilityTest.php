<?php

namespace Bakgul\LaravelTestsToReadme\Tests\LaravelTestsToReadmeTests\UtilityTests;

use Bakgul\LaravelTestsToReadme\Tests\TestCase;
use Bakgul\LaravelTestsToReadme\Utilities\SetTitle;

class SetTitleUtilityTest extends TestCase
{
    /** @test */
    public function it_will_return_title_with_header_level(): void
    {
        $this->assertEquals('## ClassName', SetTitle::_('class', 'ClassName'));

        $this->assertEquals('### MethodName', SetTitle::_('method', 'MethodName'));
    }
}
