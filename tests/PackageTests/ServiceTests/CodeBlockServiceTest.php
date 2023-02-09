<?php

namespace Bakgul\LaravelTestsToReadme\Tests\PackageTests\ServiceTests;

use Bakgul\LaravelTestsToReadme\Services\CodeBlockService;
use Bakgul\LaravelTestsToReadme\Tests\TestCase;
use Bakgul\LaravelTestsToReadme\Tests\TestConcerns\HasInteraction;
use Bakgul\LaravelTestsToReadme\Tests\TestConcerns\HasScenarios;
use ReflectionClass;

class CodeBlockServiceTest extends TestCase
{
    use HasInteraction, HasScenarios;

    public function tearDown(): void
    {
        $this->file->delete();

        parent::tearDown();
    }
    /** @test */
    public function it_will_generate_code_block_when_everything_went_well(): void
    {
        $this->file('base', '');

        $this->assertEquals(
            [
                "// It will test but its name is not imported except the fact that it will be\n// printed to readnme file.",
                "",
                "// something to write",
                "",
                "{$this->class()}::dummyMethod(1, 2);",
                "",
                "// [1, 2]",
            ],
            (new CodeBlockService($this->props()))()
        );
    }

    /** @test */
    public function it_will_print_the_result_passed_in_props_rather_than_trying_to_produce_one(): void
    {
        $this->file('base', '');

        $this->assertEquals(
            [
                "// It will test but its name is not imported except the fact that it will be\n// printed to readnme file.",
                "",
                "// something to write",
                "",
                "{$this->class()}::dummyMethod(1, 2);",
                "",
                "// [1, 2, 3]",
            ],
            (new CodeBlockService($this->props([
                'result' => '[1, 2, 3]'
            ])))()
        );
    }

    /** @test */
    public function it_will_not_add_extra_empty_line_when_a_part_is_not_printed(): void
    {
        $this->file('base', '');

        $this->assertEquals(
            [
                "// It will test but its name is not imported except the fact that it will be\n// printed to readnme file.",
                "",
                "{$this->class()}::dummyMethod(1, 2);",
                "",
                "// [1, 2]",
            ],
            (new CodeBlockService($this->props([
                'message' => null
            ])))()
        );
    }

    /** @test */
    public function it_will_break_arguments_to_multiple_line_when_they_are_longer_or_more_than_threshold(): void
    {
        config()->set('to-readme.line_length', 60);

        $this->file('long', '');

        $this->assertEquals(
            [
                "// It will test but its name is not imported except the\n// fact that it will be printed to readnme file.",
                "",
                <<<EOD
                {$this->class()}::dummyMethod(
                    1111111, 
                    2222222, 
                    3333333, 
                    4444444
                );
                EOD,
                "",
                "// [5555555, 5555555]",
            ],
            (new CodeBlockService($this->props([
                'message' => null,
                'args' => [
                    1111111,
                    2222222,
                    3333333,
                    4444444
                ]
            ])))()
        );
    }

    private function class(): string
    {
        return $this->file->className();
    }

    private function props(array $specs = []): array
    {
        $implementation = $this->implementation($specs);

        return [
            ...$this->implementation($specs),
            'test' => 'it_will_test_but_its_name_is_not_imported_except_the_fact_that_it_will_be_printed_to_readnme_file',
            'class' => $r = new ReflectionClass($implementation['class']),
            'method' => $r->getMethod($implementation['method']),
        ];
    }

    private function implementation(array $specs): array
    {
        return [
            'class' => $this->file->class(),
            'method' => 'dummyMethod',
            'args' => [1, 2],
            'message' => 'something to write',
            ...$specs
        ];
    }

    private array $scenarios = [
        'base' => 'public static function dummyMethod(int $num1, int $num2): array { return  [$num1, $num2]; }',
        'long' => 'public static function dummyMethod(int $num1, int $num2, int $num4, int $num3): array { return  [$num1 + $num3, $num2 + $num4]; }'
    ];
}
