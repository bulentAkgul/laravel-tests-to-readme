<?php

namespace Bakgul\LaravelTestsToReadme\Tests\PackageTests\ResolverTests;

use Bakgul\LaravelTestsToReadme\Helpers\Merge;
use Bakgul\LaravelTestsToReadme\Resolvers\PhpdocResolver;
use Bakgul\LaravelTestsToReadme\Tests\TestCase;
use Bakgul\LaravelTestsToReadme\Tests\TestConcerns\HasScenarios;

class PhpdocResolverTest extends TestCase
{
    use HasScenarios;

    /** @test */
    public function phpdoc_will_be_resolved_and_converted_to_assoc(): void
    {
        foreach ($this->scenarios() as $scenario) {
            $this->assertEquals(
                $this->expected($scenario),
                $this->resolve($scenario),
                $scenario
            );
        }
    }

    private function resolve($scenario): array
    {
        return (new PhpdocResolver($this->input($scenario)))();
    }

    private array $scenarios = [
        'simple' => [
            'in' => <<<EOD
                /**
                 * Dynamically pass methods to the default connection.
                 * 
                 * @param  string  \$method
                 * @param  array  \$parameters
                 * @return mixed
                */
                EOD,
            'out' => [
                'message' => ['Dynamically pass methods to the default connection.'],
                'param' => [
                    ['string', '$method'],
                    ['array', '$parameters']
                ],
                'return' => ['mixed']
            ]
        ],
        'multiple_return_types' => [
            'in' => <<<EOD
                /**
                 * Dynamically pass methods to the default connection. 
                 *
                 * @param  string \$method
                 * @param  array \$parameters
                 * @return array|string|null
                */  
                EOD,
            'out' => [
                'message' => ['Dynamically pass methods to the default connection.'],
                'param' => [
                    ['string', '$method'],
                    ['array', '$parameters']
                ],
                'return' => ['array', 'string', 'null']
            ],
        ],
        'multi_line_head' => [
            'in' => <<<EOD
                /**
                 * Dynamically pass methods to the default connection.
                 * And this is an extra line to test.
                 * 
                 * @param string \$method
                 * @param array \$parameters
                 * @return mixed
                */
                EOD,
            'out' => [
                'message' => [
                    'Dynamically pass methods to the default connection.',
                    'And this is an extra line to test.'
                ],
                'param' => [
                    ['string', '$method'],
                    ['array', '$parameters']
                ],
                'return' => ['mixed']
            ]
        ],
        'param_explanation' => [
            'in' => <<<EOD
                /**
                 * Dynamically pass methods to the default connection.
                 * 
                 * @param  string  \$method  some explanations
                 * @param  array  \$parameters some more explanations
                 * @return mixed
                */
                EOD,
            'out' => [
                'message' => ['Dynamically pass methods to the default connection.'],
                'param' => [
                    ['string', '$method', 'some explanations'],
                    ['array', '$parameters', 'some more explanations']
                ],
                'return' => ['mixed']
            ]
        ],
        'multi_line_param_explanation' => [
            'in' => <<<EOD
                /**
                 * Dynamically pass methods to the default connection.
                 * 
                 * @param  string  \$method  some explanations
                                            which need one more line.
                 * @param  array  \$parameters some more explanations
                                              and even more in the
                                              following lines.
                 * @return mixed
                */
                EOD,
            'out' => [
                'message' => ['Dynamically pass methods to the default connection.'],
                'param' => [
                    ['string', '$method', 'some explanations which need one more line.'],
                    ['array', '$parameters', 'some more explanations and even more in the following lines.']
                ],
                'return' => ['mixed']
            ]
        ],
        'param_with_multiple_types' => [
            'in' => <<<EOD
                    /**
                     * Dynamically pass methods to the default connection.
                     * 
                     * @param  string|array|null  \$method
                     * @param  array|callable  \$parameters
                     * @return mixed
                    */
                EOD,
            'out' => [
                'message' => ['Dynamically pass methods to the default connection.'],
                'param' => [
                    ['string|array|null', '$method'],
                    ['array|callable', '$parameters']
                ],
                'return' => ['mixed']
            ]
        ],
        'param_with_variadic_type' => [
            'in' => <<<EOD
                /**
                 * Dynamically pass methods to the default connection.
                 * 
                 * @param  string  \$method,...
                 * @return mixed
                */
                EOD,
            'out' => [
                'message' => ['Dynamically pass methods to the default connection.'],
                'param' => [
                    ['string', '$method,...'],
                ],
                'return' => ['mixed']
            ]
        ],
        'tag_with_no_value' => [
            'in' => <<<EOD
                /**
                 * Dynamically pass methods to the default connection.
                 * 
                 * @api
                */
                EOD,
            'out' => [
                'message' => ['Dynamically pass methods to the default connection.'],
                'api' => ''
            ]
        ],
        'tag_with_string' => [
            'in' => <<<EOD
                /**
                 * Dynamically pass methods to the default connection.
                 * 
                 * @license https://opensource.org/licenses/gpl-license.php GNU Public License
                */
                EOD,
            'out' => [
                'message' => ['Dynamically pass methods to the default connection.'],
                'license' => 'https://opensource.org/licenses/gpl-license.php',
            ]
        ]
    ];
}
