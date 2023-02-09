<?php

namespace Bakgul\LaravelTestsToReadme\Tests\PackageTests\TaskTests;

use Bakgul\LaravelTestsToReadme\Tasks\PrintVariable;
use Bakgul\LaravelTestsToReadme\Tests\TestCase;
use Bakgul\LaravelTestsToReadme\Tests\TestTasks\Reflect;

class PrintVariableTaskTest extends TestCase
{
    /** @test */
    public function it_will_print_numeric_variables_as_they_are(): void
    {
        $this->assertEquals(1, PrintVariable::_(1));

        $this->assertEquals(1.5, PrintVariable::_(1.5));
    }

    /** @test */
    public function it_will_print_boolean_variable_as_string(): void
    {
        $this->assertEquals('true', PrintVariable::_(true));

        $this->assertEquals('false', PrintVariable::_(false));
    }

    /** @test */
    public function it_will_print_class_name_of_object(): void
    {
        $this->assertEquals(Reflect::class, PrintVariable::_(new Reflect));
    }

    /** @test */
    public function it_will_print_null_as_string(): void
    {
        $this->assertEquals('null', PrintVariable::_(null));
    }

    /** @test */
    public function it_will_print_array_as_stringified_array_syntax(): void
    {
        $this->assertEquals("['a' => 1, 'b' => ['c' => 1]]", PrintVariable::_(['a' => 1, 'b' => ['c' => 1]]));
    }

    /** @test */
    public function it_will_print_other_types_by_casting_then_to_string_and_wrapping_with_single_quote(): void
    {
        $this->assertEquals("'str'", PrintVariable::_('str'));
    }
}
