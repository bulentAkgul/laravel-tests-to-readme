<?php

namespace Bakgul\LaravelTestsToReadme\Tests\LaravelTestsToReadmeTests\AdapterTests;

use Bakgul\LaravelHelpers\Helpers\Arr;
use Bakgul\LaravelTestsToReadme\Adapters\PhpdocParamToAssoc;
use Bakgul\LaravelTestsToReadme\Resolvers\PhpdocResolver;
use Bakgul\LaravelTestsToReadme\Tests\TestCase;
use Bakgul\LaravelTestsToReadme\Tests\TestConcerns\HasInteraction;
use Bakgul\LaravelTestsToReadme\Tests\TestConcerns\HasScenarios;

class PhpdocParamToAssocArrayAdapterTest extends TestCase
{
    use HasInteraction, HasScenarios;

    /** @test */
    public function it_will_convert_the_params_in_phpdoc_to_assoc_array(): void
    {
        foreach ($this->scenarios() as $scenario) {
            if ($scenario == 'complex') continue;

            $this->assertEquals(
                $this->expected($scenario),
                $this->convert($scenario),
                $scenario
            );
        }
    }

    /** @test */
    public function it_will_convert_the_params_when_they_have_some_complex_types(): void
    {
        $this->assertEquals(
            $this->expected('complex'),
            Arr::pluck(PhpdocParamToAssoc::adapt(
                (new PhpdocResolver($this->input('complex')))()['param']
            ), 'types')
        );
    }

    private function convert(string $scenario): array
    {
        return PhpdocParamToAssoc::adapt($this->input($scenario));
    }

    private array $scenarios = [
        'single' => [
            'in' => [
                ['array', '$x1']
            ],
            'out' => [
                [
                    'name' => 'x1',
                    'position' => 0,
                    'types' => [[
                        'name' => 'array',
                        'nullable' => false,
                        'builtin' => null
                    ]],
                    'isVariadic' => false,
                    'isReference' => false,
                    'explanations' => '',
                ]
            ]
        ],
        'variadic' => [
            'in' => [
                ['array', '...$x2']
            ],
            'out' => [
                [
                    'name' => 'x2',
                    'position' => 0,
                    'types' => [[
                        'name' => 'array',
                        'nullable' => false,
                        'builtin' => null
                    ]],
                    'isVariadic' => true,
                    'isReference' => false,
                    'explanations' => '',
                ]
            ]
        ],
        'reference' => [
            'in' => [
                ['array', '&$x1']
            ],
            'out' => [
                [
                    'name' => 'x1',
                    'position' => 0,
                    'types' => [[
                        'name' => 'array',
                        'nullable' => false,
                        'builtin' => null
                    ]],
                    'isVariadic' => false,
                    'isReference' => true,
                    'explanations' => '',
                ]
            ]
        ],
        'nullable' => [
            'in' => [
                ['?array', '$x1'],
                ['array|null', '$x2']
            ],
            'out' => [
                [
                    'name' => 'x1',
                    'position' => 0,
                    'types' => [[
                        'name' => 'array',
                        'nullable' => true,
                        'builtin' => null
                    ]],
                    'isVariadic' => false,
                    'isReference' => false,
                    'explanations' => '',
                ],
                [
                    'name' => 'x2',
                    'position' => 1,
                    'types' => [[
                        'name' => 'array',
                        'nullable' => true,
                        'builtin' => null
                    ]],
                    'isVariadic' => false,
                    'isReference' => false,
                    'explanations' => '',
                ]
            ]
        ],
        'multiple' => [
            'in' => [
                ['array|string', '$x1'],
                ['array|string|null', '$x2']
            ],
            'out' => [
                [
                    'name' => 'x1',
                    'position' => 0,
                    'types' => [[
                        'name' => 'array',
                        'nullable' => false,
                        'builtin' => null
                    ], [
                        'name' => 'string',
                        'nullable' => false,
                        'builtin' => null
                    ]],
                    'isVariadic' => false,
                    'isReference' => false,
                    'explanations' => '',
                ],
                [
                    'name' => 'x2',
                    'position' => 1,
                    'types' => [[
                        'name' => 'array',
                        'nullable' => true,
                        'builtin' => null
                    ], [
                        'name' => 'string',
                        'nullable' => true,
                        'builtin' => null
                    ]],
                    'isVariadic' => false,
                    'isReference' => false,
                    'explanations' => '',
                ]
            ]
        ],
        'complex' => [
            'in' => <<<EOD
            /**
             * Apply the callback if the given "value" is (or resolves to) truthy.
             *
             * @template TWhenParameter
             * @template TWhenReturnType
             *
             * @param  (\Closure(\$this): TWhenParameter)|TWhenParameter|null \$value Some multiline explanations
             *                                                                      will be here.
             * @param  (callable(\$this, TWhenParameter): TWhenReturnType)|null  \$callback Some even longet multiline 
             *                                                                            explanations will be here
             *                                                                            to be sure that it's OK.
             * @param  (callable(\$this, TWhenParameter): TWhenReturnType)|null  \$default
             * @return \$this|TWhenReturnType
             */
            public function dummyMethod(\$value = null, callable \$callback = null, callable \$default = null) {}
            EOD,
            'out' => [
                [
                    [
                        'name' => '(\Closure($this): TWhenParameter)',
                        'nullable' => true,
                        'builtin' => null,
                    ],
                    [
                        'name' => 'TWhenParameter',
                        'nullable' => true,
                        'builtin' => null,
                    ],
                ],
                [
                    [
                        'name' => '(callable($this, TWhenParameter): TWhenReturnType)',
                        'nullable' => true,
                        'builtin' => null,
                    ],
                ],
                [
                    [
                        'name' => '(callable($this, TWhenParameter): TWhenReturnType)',
                        'nullable' => true,
                        'builtin' => null,
                    ],
                ],
            ]
        ]
    ];
}
