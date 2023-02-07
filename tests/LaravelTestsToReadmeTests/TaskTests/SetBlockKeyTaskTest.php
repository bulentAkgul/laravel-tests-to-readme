<?php

namespace Bakgul\LaravelTestsToReadme\Tests\LaravelTestsToReadmeTests\TaskTests;

use Bakgul\LaravelTestsToReadme\Tasks\SetBlockKey;
use Bakgul\LaravelTestsToReadme\Tests\TestCase;

class SetBlockKeyTaskTest extends TestCase
{
    /** @test */
    public function it_will_set_key_prefix_when_line_is_a_start_of_class_or_method(): void
    {
        (new SetBlockKey('## StartClass', '', [], ''))();

        $this->assertEquals('class', config('to-readme.key_prefix'));

        (new SetBlockKey('### startMethod', '', [], ''))();

        $this->assertEquals('startMethod', config('to-readme.key_prefix'));
    }

    /** @test */
    public function it_will_return_clened_line_as_key_when_the_line_is_a_start_of_class_or_method(): void
    {
        $this->assertEquals('StartClass', (new SetBlockKey('## StartClass', '', [], ''))());

        $this->assertEquals('startMethod', (new SetBlockKey('### startMethod', '', [], ''))());
    }

    /** @test */
    public function it_will_return_prefixed_key_when_key_prefix_is_set(): void
    {
        (new SetBlockKey('## StartClass', '', [], ''))();

        $this->assertEquals(
            ['class', 'textblock'],
            array_slice(explode('-', (new SetBlockKey('a paragraph under class title', '', [], ''))()), 0, 2)
        );

        (new SetBlockKey('### startMethod', '', [], ''))();

        $this->assertEquals(
            ['startMethod', 'codeblock'],
            array_slice(explode('-', (new SetBlockKey('```php', '', [], ''))()), 0, 2)
        );
    }
}
