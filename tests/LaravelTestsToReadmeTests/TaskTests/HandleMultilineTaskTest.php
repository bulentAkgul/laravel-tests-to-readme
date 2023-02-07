<?php

namespace Bakgul\LaravelTestsToReadme\Tests\LaravelTestsToReadmeTests\TaskTests;

use Bakgul\LaravelTestsToReadme\Tasks\HandleMultiline;
use Bakgul\LaravelTestsToReadme\Tests\TestCase;

class HandleMultilineTaskTest extends TestCase
{
    /** @test */
    public function it_will_return_empty_string_when_line_is_short(): void
    {
        $this->assertEmpty((new HandleMultiline(['public function name', ['x', 'y']]))());
    }

    /** @test */
    public function it_will_return_empty_string_when_line_is_short_even_if_the_arg_count_is_more_than_threshold(): void
    {
        $this->assertEmpty((new HandleMultiline(['public function name', ['a', 'b', 'c', 'd', 'e']]))());
    }

    /** @test */
    public function it_will_return_arguments_in_multiple_lines_when_the_line_is_to_long(): void
    {
        $this->assertEquals(
            <<<EOD
                xxxxx,
                yyyyy,
            EOD,
            (new HandleMultiline(['public function aVeryLongMethodNameToMakeThisMultiLineWithTwoArgs', ['xxxxx', 'yyyyy']]))()
        );
    }

    /** @test */
    public function it_will_return_args_in_multiple_lines_when_args_are_more_than_force_limit(): void
    {
        $this->assertEquals(
            <<<EOD
                aaa,
                bbb,
                ccc,
                ddd,
                eee,
                fff,
            EOD,
            (new HandleMultiline(['public function name', ['aaa', 'bbb', 'ccc', 'ddd', 'eee', 'fff']]))()
        );
    }
}
