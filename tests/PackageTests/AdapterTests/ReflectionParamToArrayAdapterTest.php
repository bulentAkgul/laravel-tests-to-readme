<?php

namespace Bakgul\LaravelTestsToReadme\Tests\PackageTests\AdapterTests;

use Bakgul\LaravelTestsToReadme\Adapters\ReflectionParamToAssoc;
use Bakgul\LaravelTestsToReadme\Tests\TestCase;
use Bakgul\LaravelTestsToReadme\Tests\TestConcerns\HasInteraction;
use Bakgul\LaravelTestsToReadme\Tests\TestConcerns\HasScenarios;
use Bakgul\LaravelTestsToReadme\Tests\TestTasks\Reflect;

class ReflectionParamToArrayAdapterTest extends TestCase
{
    use HasInteraction, HasScenarios;

    /** @test */
    public function it_will_convert_the_params_in_reflection_to_assoc_array(): void
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
        return ReflectionParamToAssoc::adapt(
            Reflect::method($this->file->className())->getParameters()
        );
    }

    private array $scenarios = [
        'no_type' => [
            'in' => 'public function dummyMethod($x1) {}',
            'out' => [
                [
                    'name' => 'x1',
                    'position' => 0,
                    'types' => [],
                    'hasDefault' => false,
                    'default' => null,
                    'isVariadic' => false,
                    'isReference' => false,
                ]
            ],
        ],
        // 'one_type' => [
        //     'in' => 'public function dummyMethod(array $x1) {}',
        //     'out' => [
        //         [
        //             'name' => 'x1',
        //             'position' => 0,
        //             'types' => [
        //                 [
        //                     'name' => 'array',
        //                     'nullable' => false,
        //                     'builtin' => true,
        //                 ]
        //             ],
        //             'hasDefault' => false,
        //             'default' => null,
        //             'isVariadic' => false,
        //             'isReference' => false,
        //         ]
        //     ],
        // ],
        // 'multi_type' => [
        //     'in' => 'public function dummyMethod(array|string $x1) {}',
        //     'out' => [
        //         [
        //             'name' => 'x1',
        //             'position' => 0,
        //             'types' => [
        //                 [
        //                     'name' => 'array',
        //                     'nullable' => false,
        //                     'builtin' => true,
        //                 ],
        //                 [
        //                     'name' => 'string',
        //                     'nullable' => false,
        //                     'builtin' => true,
        //                 ]
        //             ],
        //             'hasDefault' => false,
        //             'default' => null,
        //             'isVariadic' => false,
        //             'isReference' => false,
        //         ]
        //     ],
        // ],
        // 'variadic' => [
        //     'in' => 'public function dummyMethod(...$x1) {}',
        //     'out' => [
        //         [
        //             'name' => 'x1',
        //             'position' => 0,
        //             'types' => [],
        //             'hasDefault' => false,
        //             'default' => null,
        //             'isVariadic' => true,
        //             'isReference' => false,
        //         ]
        //     ],
        // ],
        // 'nullable' => [
        //     'in' => 'public function dummyMethod(array|string|null $x1, ?string $x2) {}',
        //     'out' => [
        //         [
        //             'name' => 'x1',
        //             'position' => 0,
        //             'types' => [
        //                 [
        //                     'name' => 'array',
        //                     'nullable' => true,
        //                     'builtin' => true,
        //                 ],
        //                 [
        //                     'name' => 'string',
        //                     'nullable' => true,
        //                     'builtin' => true,
        //                 ]
        //             ],
        //             'hasDefault' => false,
        //             'default' => null,
        //             'isVariadic' => false,
        //             'isReference' => false,
        //         ],
        //         [
        //             'name' => 'x2',
        //             'position' => 1,
        //             'types' => [
        //                 [
        //                     'name' => 'string',
        //                     'nullable' => true,
        //                     'builtin' => true,
        //                 ]
        //             ],
        //             'hasDefault' => false,
        //             'default' => null,
        //             'isVariadic' => false,
        //             'isReference' => false,
        //         ]
        //     ],
        // ],
        // 'with_default' => [
        //     'in' => 'public function dummyMethod(array|string $x1 = []) {}',
        //     'out' => [
        //         [
        //             'name' => 'x1',
        //             'position' => 0,
        //             'types' => [
        //                 [
        //                     'name' => 'array',
        //                     'nullable' => false,
        //                     'builtin' => true,
        //                 ],
        //                 [
        //                     'name' => 'string',
        //                     'nullable' => false,
        //                     'builtin' => true,
        //                 ]
        //             ],
        //             'hasDefault' => true,
        //             'default' => [],
        //             'isVariadic' => false,
        //             'isReference' => false,
        //         ]
        //     ],
        // ],
        // 'multi_params' => [
        //     'in' => 'public function dummyMethod(array $x1, ?string $x2, array|string $x3 = []) {}',
        //     'out' => [
        //         [
        //             'name' => 'x1',
        //             'position' => 0,
        //             'types' => [
        //                 [
        //                     'name' => 'array',
        //                     'nullable' => false,
        //                     'builtin' => true,
        //                 ]
        //             ],
        //             'hasDefault' => false,
        //             'default' => null,
        //             'isVariadic' => false,
        //             'isReference' => false,
        //         ],
        //         [
        //             'name' => 'x2',
        //             'position' => 1,
        //             'types' => [
        //                 [
        //                     'name' => 'string',
        //                     'nullable' => true,
        //                     'builtin' => true,
        //                 ]
        //             ],
        //             'hasDefault' => false,
        //             'default' => null,
        //             'isVariadic' => false,
        //             'isReference' => false,
        //         ],
        //         [
        //             'name' => 'x3',
        //             'position' => 2,
        //             'types' => [
        //                 [
        //                     'name' => 'array',
        //                     'nullable' => false,
        //                     'builtin' => true,
        //                 ],
        //                 [
        //                     'name' => 'string',
        //                     'nullable' => false,
        //                     'builtin' => true,
        //                 ]
        //             ],
        //             'hasDefault' => true,
        //             'default' => [],
        //             'isVariadic' => false,
        //             'isReference' => false,
        //         ]
        //     ],
        // ],
    ];
}
