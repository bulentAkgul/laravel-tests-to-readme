<?php

namespace Bakgul\LaravelTestsToReadme\Tests\LaravelTestsToReadmeTests\AdapterTests;

use Bakgul\LaravelTestsToReadme\Adapters\ReflectionTypesToArray;
use Bakgul\LaravelTestsToReadme\Tests\TestCase;
use Bakgul\LaravelTestsToReadme\Tests\TestConcerns\HasInteraction;
use Bakgul\LaravelTestsToReadme\Tests\TestConcerns\HasScenarios;
use Bakgul\LaravelTestsToReadme\Tests\TestTasks\Reflect;

class ReflectionTypesToArrayAdapterTest extends TestCase
{
    use HasInteraction, HasScenarios;

    /** @test */
    public function it_will_convert_given_types_to_associative_array(): void
    {
        foreach ($this->scenarios() as $scenario) {
            $this->file($scenario);

            $this->assertEquals(
                $this->expected($scenario),
                $this->convert(),
                $scenario
            );

            $this->deleteClass();
        }
    }

    private function convert(): array
    {
        return ReflectionTypesToArray::adapt(
            Reflect::types($this->file->className())[0]
        );
    }

    private array $scenarios = [
        'no_type' => [
            'in' => 'public function dummyMethod($x1) {}',
            'out' => [],
        ],
        'one_type' => [
            'in' => 'public function dummyMethod(array $x1) {}',
            'out' => [
                [
                    'name' => 'array',
                    'nullable' => false,
                    'builtin' => true,
                ]
            ],
        ],
        'multi_type' => [
            'in' => 'public function dummyMethod(array|string $x1) {}',
            'out' => [
                [
                    'name' => 'array',
                    'nullable' => false,
                    'builtin' => true,
                ],
                [
                    'name' => 'string',
                    'nullable' => false,
                    'builtin' => true,
                ]
            ],
        ],
    ];
}
