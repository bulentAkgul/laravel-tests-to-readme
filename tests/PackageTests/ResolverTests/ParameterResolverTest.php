<?php

namespace Bakgul\LaravelTestsToReadme\Tests\PackageTests\ResolverTests;

use Bakgul\LaravelTestsToReadme\Helpers\Merge;
use Bakgul\LaravelTestsToReadme\Resolvers\ParameterResolver;
use Bakgul\LaravelTestsToReadme\Resolvers\PhpdocResolver;
use Bakgul\LaravelTestsToReadme\Tests\TestCase;
use Bakgul\LaravelTestsToReadme\Tests\TestConcerns\HasInteraction;
use Bakgul\LaravelTestsToReadme\Tests\TestConcerns\HasScenarios;
use Bakgul\LaravelTestsToReadme\Tests\TestTasks\Reflect;

class ParameterResolverTest extends TestCase
{
    use HasInteraction, HasScenarios;

    /** @test */
    public function it_will_return_method_params_when_phpdoc_and_method_have_the_same_setups(): void
    {
        $this->file('same');

        $this->assertEquals(
            $this->expected('same'),
            $this->resolve(),
        );

        $this->deleteClass();
    }

    /** @test */
    public function it_will_return_method_params_when_phpdoc_and_method_have_different_setups_and_merge_is_not_allowed(): void
    {
        Merge::set(false);

        $this->file('different');

        $this->assertEquals(
            $this->expected('different'),
            $this->resolve(),
        );

        $this->deleteClass();
    }

    /** @test */
    public function it_will_return_merged_params_when_method_has_no_typehint_and_merge_is_allowed(): void
    {
        Merge::set(true);

        $this->file('phpdoc');

        $this->assertEquals(
            $this->expected('phpdoc'),
            $this->resolve(),
        );

        $this->deleteClass();
    }


    /** @test */
    public function it_will_return_method_params_when_method_has_typehint_regardless_of_other_settings_and_phpdoc(): void
    {
        foreach ([true, false] as $mergable) {
            Merge::set($mergable);

            $this->file('typehint');

            $this->assertEquals(
                $this->expected('typehint'),
                $this->resolve(),
            );

            $this->deleteClass();
        }
    }

    private function resolve(): array
    {
        return (new ParameterResolver($this->typehint(), $this->phpdoc()))();
    }

    private function typehint(): array
    {
        return Reflect::parameters($this->file->className());
    }

    private function phpdoc(): array
    {
        return (new PhpdocResolver(Reflect::phpdoc($this->file->fileName())))()['param'] ?? [];
    }

    private array $scenarios = [
        'different' => [
            'in' => <<<EOD
                /**
                 * @param array|string \$x1
                 * @param callable \$x2
                 */ 
                public function dummyMethod(array \$x1, ?string \$x2 = '') {}
            EOD,
            'out' => [
                [
                    'name' => 'x1',
                    'position' => 0,
                    'types' => [
                        [
                            'name' => 'array',
                            'nullable' => false,
                            'builtin' => true,
                        ],
                    ],
                    'isVariadic' => false,
                    'isReference' => false,
                    'hasDefault' => false,
                    'default' => null,
                    'warning' => 'mismatched type count',
                ],
                [
                    'name' => 'x2',
                    'position' => 1,
                    'types' => [
                        [
                            'name' => 'string',
                            'nullable' => true,
                            'builtin' => true,
                        ]
                    ],
                    'isVariadic' => false,
                    'isReference' => false,
                    'hasDefault' => true,
                    'default' => '',
                    'warning' => 'mismatched types',
                ]
            ]
        ],
        'same' => [
            'in' => <<<EOD
                /**
                 * @param string \$x1
                 * @param ?callable \$x2
                 */ 
                public function dummyMethod(string &\$x1, ?callable \$x2) {}
            EOD,
            'out' => [
                [
                    'name' => 'x1',
                    'position' => 0,
                    'types' => [
                        [
                            'name' => 'string',
                            'nullable' => false,
                            'builtin' => true,
                        ],
                    ],
                    'isVariadic' => false,
                    'isReference' => true,
                    'hasDefault' => false,
                    'default' => null,
                    'warning' => 'mismatched reference parameter',
                ],
                [
                    'name' => 'x2',
                    'position' => 1,
                    'types' => [
                        [
                            'name' => 'callable',
                            'nullable' => true,
                            'builtin' => true,
                        ]
                    ],
                    'isVariadic' => false,
                    'isReference' => false,
                    'hasDefault' => false,
                    'default' => null,
                    'warning' => '',
                ]
            ]
        ],
        'typehint' => [
            'in' => <<<EOD
                /**
                 * Some explanations
                 * 
                 * @api
                 */ 
                public function dummyMethod(array|string \$x1, callable \$x2) {}
            EOD,
            'out' => [
                [
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
                    'isVariadic' => false,
                    'isReference' => false,
                    'hasDefault' => false,
                    'default' => null,
                    'warning' => '',
                ],
                [
                    'name' => 'x2',
                    'position' => 1,
                    'types' => [
                        [
                            'name' => 'callable',
                            'nullable' => false,
                            'builtin' => true,
                        ]
                    ],
                    'isVariadic' => false,
                    'isReference' => false,
                    'hasDefault' => false,
                    'default' => null,
                    'warning' => '',
                ]
            ]
        ],
        'phpdoc' => [
            'in' => <<<EOD
                /**
                 * @param array|string \$x1
                 * @param callable \$x2
                 */ 
                public function dummyMethod(\$x1, \$x2) {}
            EOD,
            'out' => [
                [
                    'name' => 'x1',
                    'position' => 0,
                    'types' => [
                        [
                            'name' => 'array',
                            'nullable' => false,
                            'builtin' => null,
                        ],
                        [
                            'name' => 'string',
                            'nullable' => false,
                            'builtin' => null,
                        ]
                    ],
                    'isVariadic' => false,
                    'isReference' => false,
                    'hasDefault' => false,
                    'default' => null,
                    'warning' => '',
                ],
                [
                    'name' => 'x2',
                    'position' => 1,
                    'types' => [
                        [
                            'name' => 'callable',
                            'nullable' => false,
                            'builtin' => null,
                        ]
                    ],
                    'isVariadic' => false,
                    'isReference' => false,
                    'hasDefault' => false,
                    'default' => null,
                    'warning' => '',
                ]
            ]
        ]
    ];
}
