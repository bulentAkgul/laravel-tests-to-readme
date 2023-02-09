<?php

namespace Bakgul\LaravelTestsToReadme\Tests\PackageTests\AdapterTests;

use Bakgul\LaravelTestsToReadme\Adapters\PhpdocTypesToArray;
use Bakgul\LaravelTestsToReadme\Tests\TestCase;
use Bakgul\LaravelTestsToReadme\Tests\TestConcerns\HasScenarios;

class PhpdocTypesToArrayAdapterTest extends TestCase
{
    use HasScenarios;

    /** @test */
    public function it_will_convert_given_types_to_associative_array(): void
    {
        foreach ($this->scenarios() as $scenario) {
            $this->assertEquals(
                $this->expected($scenario),
                $this->convert($scenario),
                $scenario
            );
        }
    }

    private function convert(string $scenario): array
    {
        return PhpdocTypesToArray::adapt($this->input($scenario));
    }

    private array $scenarios = [
        'simple' => [
            'in' => 'array',
            'out' => [
                [
                    'name' => 'array',
                    'nullable' => false,
                    'builtin' => null
                ]
            ]
        ],
        'multiple' => [
            'in' => 'array|string',
            'out' => [
                [
                    'name' => 'array',
                    'nullable' => false,
                    'builtin' => null
                ],
                [
                    'name' => 'string',
                    'nullable' => false,
                    'builtin' => null
                ]
            ]
        ],
        'nullable_with_null' => [
            'in' => 'array|null',
            'out' => [
                [
                    'name' => 'array',
                    'nullable' => true,
                    'builtin' => null
                ]
            ]
        ],
        'nullable_with_question_mark' => [
            'in' => '?array',
            'out' => [
                [
                    'name' => 'array',
                    'nullable' => true,
                    'builtin' => null
                ]
            ]
        ],
        'multiple_nullable' => [
            'in' => 'array|string|null',
            'out' => [
                [
                    'name' => 'array',
                    'nullable' => true,
                    'builtin' => null
                ],
                [
                    'name' => 'string',
                    'nullable' => true,
                    'builtin' => null
                ]
            ]
        ],
    ];
}
