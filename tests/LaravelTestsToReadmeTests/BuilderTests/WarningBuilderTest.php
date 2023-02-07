<?php

namespace Bakgul\LaravelTestsToReadme\Tests\LaravelTestsToReadmeTests\BuilderTests;

use Bakgul\LaravelTestsToReadme\Builders\WarningBuilder;
use Bakgul\LaravelTestsToReadme\Tests\TestCase;
use Bakgul\LaravelTestsToReadme\Tests\TestConcerns\HasScenarios;

class WarningBuilderTest extends TestCase
{
    use HasScenarios;

    /** @test */
    public function it_will_build_a_warning_block_to_display_on_readme_file(): void
    {
        foreach ($this->scenarios() as $scenario) {
            $this->assertEquals(
                $this->expected($scenario),
                $this->build($scenario),
            );
        }
    }

    private function build(string $scenario): string
    {
        return (new WarningBuilder)($this->input($scenario));
    }

    private array $scenarios = [
        'no_warning' => [
            'in' => [
                ['warning' => ''],
            ],
            'out' => ''
        ],
        'one_warning' => [
            'in' => [
                ['warning' => 'warning 1'],
            ],
            'out' => "WARNINGS:\n- warning 1\n"
        ],
        'one_unique_warning' => [
            'in' => [
                ['warning' => 'warning 1'],
                ['warning' => 'warning 1'],
            ],
            'out' => "WARNINGS:\n- warning 1\n"
        ],
        'multiple_warnings' => [
            'in' => [
                ['warning' => 'warning 1'],
                ['warning' => 'warning 2'],
            ],
            'out' => "WARNINGS:\n- warning 1\n- warning 2\n"
        ],
    ];
}
