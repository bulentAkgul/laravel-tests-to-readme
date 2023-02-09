<?php

namespace Bakgul\LaravelTestsToReadme\Tests\PackageTests\TaskTests;

use Bakgul\LaravelTestsToReadme\Tasks\WarnForInconsistentInputs;
use Bakgul\LaravelTestsToReadme\Tests\TestCase;

class WarnForInconsistentInputsTaskTest extends TestCase
{
    private function expected(string $key): string
    {
        return config("to-readme.warnings.{$key}");
    }

    /** @test */
    public function it_will_not_warn_when_parameters_are_consistent(): void
    {
        $this->assertEquals('', (new WarnForInconsistentInputs(
            $this->scenarios['parameter']['no_warning']['consistent']['same'],
            $this->scenarios['parameter']['no_warning']['consistent']['same']
        ))());
    }

    /** @test */
    public function it_will_not_warn_when_phpdoc_or_method_has_no_parameters(): void
    {
        $this->assertEquals('', (new WarnForInconsistentInputs(
            $this->scenarios['parameter']['no_warning']['consistent']['same'],
            []
        ))());
    }

    /** @test */
    public function it_will_not_warn_when_parameters_has_no_type_hint(): void
    {
        $this->assertEquals('', (new WarnForInconsistentInputs(
            $this->scenarios['parameter']['no_warning']['no_typehint']['method'],
            $this->scenarios['parameter']['no_warning']['no_typehint']['phpdoc'],
        ))());
    }

    /** @test */
    public function it_will_not_warn_when_return_types_are_only_specified_on_method(): void
    {
        $this->assertEquals('', (new WarnForInconsistentInputs(
            $this->scenarios['return']['no_warning']['consistent']['same'],
            $this->scenarios['return']['no_warning']['consistent']['same'],
            null
        ))());
    }

    /** @test */
    public function it_will_not_warn_when_return_types_are_only_specified_on_phpdoc(): void
    {
        $this->assertEquals('', (new WarnForInconsistentInputs(
            $this->scenarios['return']['no_warning']['no_typehint']['method'],
            $this->scenarios['return']['no_warning']['no_typehint']['phpdoc'],
            null
        ))());
    }

    /** @test */
    public function it_will_not_warn_when_return_types_are_only_specified_on_typehint(): void
    {
        $this->assertEquals('', (new WarnForInconsistentInputs(
            $this->scenarios['return']['no_warning']['no_phpdoc']['method'],
            $this->scenarios['return']['no_warning']['no_phpdoc']['phpdoc'],
            null
        ))());
    }

    /** @test */
    public function it_will_warn_when_the_counts_of_parameters_are_inconsistent(): void
    {
        $this->assertEquals(
            $this->expected('mismatched_parameter_count'),
            (new WarnForInconsistentInputs(
                $this->scenarios['parameter']['warnings']['mismatched_parameter_count']['method'],
                $this->scenarios['parameter']['warnings']['mismatched_parameter_count']['phpdoc'],
            ))()
        );
    }

    /** @test */
    public function it_will_warn_when_the_order_of_parameters_are_inconsistent(): void
    {
        $this->assertEquals(
            $this->expected('misplaced_parameter'),
            (new WarnForInconsistentInputs(
                $this->scenarios['parameter']['warnings']['misplaced_parameter']['method'],
                $this->scenarios['parameter']['warnings']['misplaced_parameter']['phpdoc'],
            ))()
        );
    }

    /** @test */
    public function it_will_warn_when_variadism_of_parameters_are_inconsistent(): void
    {
        $this->assertEquals(
            $this->expected('mismatched_variadic'),
            (new WarnForInconsistentInputs(
                $this->scenarios['parameter']['warnings']['mismatched_variadic']['method'],
                $this->scenarios['parameter']['warnings']['mismatched_variadic']['phpdoc'],
            ))()
        );
    }

    /** @test */
    public function it_will_warn_when_the_reference_indicator_of_parameters_are_inconsistent(): void
    {
        $this->assertEquals(
            $this->expected('mismatched_variadic'),
            (new WarnForInconsistentInputs(
                $this->scenarios['parameter']['warnings']['mismatched_variadic']['method'],
                $this->scenarios['parameter']['warnings']['mismatched_variadic']['phpdoc'],
            ))()
        );
    }

    /** @test */
    public function it_will_warn_when_the_counts_of_types_are_inconsistent(): void
    {
        $this->assertEquals(
            $this->expected('mismatched_type_count'),
            (new WarnForInconsistentInputs(
                $this->scenarios['parameter']['type_warnings']['mismatched_type_count']['method'],
                $this->scenarios['parameter']['type_warnings']['mismatched_type_count']['phpdoc'],
            ))()
        );
    }

    /** @test */
    public function it_will_warn_when_the_types_are_inconsistent(): void
    {
        $this->assertEquals(
            $this->expected('mismatched_types'),
            (new WarnForInconsistentInputs(
                $this->scenarios['parameter']['type_warnings']['mismatched_types']['method'],
                $this->scenarios['parameter']['type_warnings']['mismatched_types']['phpdoc'],
            ))()
        );
    }

    /** @test */
    public function it_will_warn_when_the_nullablity_of_the_types_are_inconsistent(): void
    {
        $this->assertEquals(
            $this->expected('missmatched_nullable_types'),
            (new WarnForInconsistentInputs(
                $this->scenarios['parameter']['type_warnings']['missmatched_nullable_types']['method'],
                $this->scenarios['parameter']['type_warnings']['missmatched_nullable_types']['phpdoc'],
            ))()
        );
    }

    /** @test */
    public function it_will_warn_when_the_counts_of_return_types_are_inconsistent(): void
    {
        $this->assertEquals(
            $this->expected('mismatched_type_count'),
            (new WarnForInconsistentInputs(
                $this->scenarios['return']['warnings']['mismatched_type_count']['method'],
                $this->scenarios['return']['warnings']['mismatched_type_count']['phpdoc'],
                null
            ))()
        );
    }

    /** @test */
    public function it_will_warn_when_the_return_types_are_inconsistent(): void
    {
        $this->assertEquals(
            $this->expected('mismatched_types'),
            (new WarnForInconsistentInputs(
                $this->scenarios['return']['warnings']['mismatched_types']['method'],
                $this->scenarios['return']['warnings']['mismatched_types']['phpdoc'],
                null
            ))()
        );
    }

    /** @test */
    public function it_will_warn_when_the_nullablity_of_the_return_types_are_inconsistent(): void
    {
        $this->assertEquals(
            $this->expected('missmatched_nullable_types'),
            (new WarnForInconsistentInputs(
                $this->scenarios['return']['warnings']['missmatched_nullable_types']['method'],
                $this->scenarios['return']['warnings']['missmatched_nullable_types']['phpdoc'],
                null
            ))()
        );
    }

    private array $scenarios = [
        'parameter' => [
            'no_warning' => [
                'consistent' => [
                    'same' => [[
                        'name' => 'x1',
                        'position' => 0,
                        'types' => [
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
                        'hasDefault' => false,
                        'default' => null,
                        'isVariadic' => false,
                        'isReference' => false,
                    ]],
                ],
                'no_typehint' => [
                    'phpdoc' => [[
                        'name' => 'x1',
                        'position' => 0,
                        'types' => [
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
                        'hasDefault' => false,
                        'default' => null,
                        'isVariadic' => false,
                        'isReference' => false,
                    ]],
                    'method' =>  [[
                        'name' => 'x1',
                        'position' => 0,
                        'types' => [],
                        'hasDefault' => false,
                        'default' => null,
                        'isVariadic' => false,
                        'isReference' => false,
                    ]],
                ],
            ],
            'warnings' => [
                'mismatched_parameter_count' => [
                    'method' => [['placeholder']],
                    'phpdoc' => [['placeholder'], ['placeholder']],
                ],
                'misplaced_parameter' => [
                    'phpdoc' => [
                        [
                            'name' => 'x1',
                            'position' => 0,
                            'types' => [],
                            'hasDefault' => false,
                            'default' => null,
                            'isVariadic' => false,
                            'isReference' => false,
                        ],
                        [
                            'name' => 'x2',
                            'position' => 1,
                            'types' => [],
                            'hasDefault' => false,
                            'default' => null,
                            'isVariadic' => false,
                            'isReference' => false,
                        ]
                    ],
                    'method' =>  [
                        [
                            'name' => 'x2',
                            'position' => 0,
                            'types' => [],
                            'hasDefault' => false,
                            'default' => null,
                            'isVariadic' => false,
                            'isReference' => false,
                        ],
                        [
                            'name' => 'x1',
                            'position' => 1,
                            'types' => [],
                            'hasDefault' => false,
                            'default' => null,
                            'isVariadic' => false,
                            'isReference' => false,
                        ]
                    ],
                ],
                'mismatched_variadic' => [
                    'method' => [[
                        'name' => 'x1',
                        'position' => 0,
                        'types' => [
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
                        'hasDefault' => false,
                        'default' => null,
                        'isVariadic' => true,
                        'isReference' => false,
                    ]],
                    'phpdoc' => [[
                        'name' => 'x1',
                        'position' => 0,
                        'types' => [
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
                        'hasDefault' => false,
                        'default' => null,
                        'isVariadic' => false,
                        'isReference' => false,
                    ]],
                ],
                'mismatched_reference' => [
                    'method' => [[
                        'name' => 'x1',
                        'position' => 0,
                        'types' => [
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
                        'hasDefault' => false,
                        'default' => null,
                        'isVariadic' => false,
                        'isReference' => true,
                    ]],
                    'phpdoc' => [[
                        'name' => 'x1',
                        'position' => 0,
                        'types' => [
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
                        'hasDefault' => false,
                        'default' => null,
                        'isVariadic' => false,
                        'isReference' => false,
                    ]],
                ],
            ],
            'type_warnings' => [
                'mismatched_type_count' => [
                    'method' => [[
                        'name' => 'x1',
                        'position' => 0,
                        'types' => [
                            [
                                'name' => 'array',
                                'nullable' => false,
                                'builtin' => true,
                            ]
                        ],
                        'hasDefault' => false,
                        'default' => null,
                        'isVariadic' => false,
                        'isReference' => false,
                    ]],
                    'phpdoc' => [[
                        'name' => 'x1',
                        'position' => 0,
                        'types' => [
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
                        'hasDefault' => false,
                        'default' => null,
                        'isVariadic' => false,
                        'isReference' => false,
                    ]],
                ],
                'mismatched_types' => [
                    'method' => [[
                        'name' => 'x1',
                        'position' => 0,
                        'types' => [
                            [
                                'name' => 'string',
                                'nullable' => false,
                                'builtin' => true,
                            ]
                        ],
                        'hasDefault' => false,
                        'default' => null,
                        'isVariadic' => false,
                        'isReference' => false,
                    ]],
                    'phpdoc' => [[
                        'name' => 'x1',
                        'position' => 0,
                        'types' => [
                            [
                                'name' => 'array',
                                'nullable' => false,
                                'builtin' => true,
                            ]
                        ],
                        'hasDefault' => false,
                        'default' => null,
                        'isVariadic' => false,
                        'isReference' => false,
                    ]],
                ],
                'missmatched_nullable_types' => [
                    'method' => [[
                        'name' => 'x1',
                        'position' => 0,
                        'types' => [
                            [
                                'name' => 'array',
                                'nullable' => true,
                                'builtin' => true,
                            ]
                        ],
                        'hasDefault' => false,
                        'default' => null,
                        'isVariadic' => false,
                        'isReference' => false,
                    ]],
                    'phpdoc' => [[
                        'name' => 'x1',
                        'position' => 0,
                        'types' => [
                            [
                                'name' => 'array',
                                'nullable' => false,
                                'builtin' => true,
                            ],
                        ],
                        'hasDefault' => false,
                        'default' => null,
                        'isVariadic' => false,
                        'isReference' => false,
                    ]],
                ],
            ]
        ],
        'return' => [
            'no_warning' => [
                'consistent' => [
                    'same' => ['array', 'string'],
                ],
                'no_typehint' => [
                    'phpdoc' => ['array', 'string'],
                    'method' => [],
                ],
                'no_phpdoc' => [
                    'method' => ['array', 'string'],
                    'phpdoc' => [],
                ],
            ],
            'warnings' => [
                'mismatched_type_count' => [
                    'method' => ['array'],
                    'phpdoc' => ['array', 'string'],
                ],
                'mismatched_types' => [
                    'method' => ['array', 'string'],
                    'phpdoc' => ['array', 'int'],
                ],
                'missmatched_nullable_types' => [
                    'method' => ['array', 'null'],
                    'phpdoc' => ['array'],
                ],
            ]
        ],
    ];
}
