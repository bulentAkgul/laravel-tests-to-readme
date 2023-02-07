<?php

namespace Bakgul\LaravelTestsToReadme\Tests\LaravelTestsToReadmeTests\BuilderTests;

use Bakgul\LaravelTestsToReadme\Builders\PhpdocBuilder;
use Bakgul\LaravelTestsToReadme\Helpers\Merge;
use Bakgul\LaravelTestsToReadme\Resolvers\PhpdocResolver;
use Bakgul\LaravelTestsToReadme\Tests\TestCase;
use Bakgul\LaravelTestsToReadme\Tests\TestConcerns\HasScenarios;

class PhpdocBuilderTest extends TestCase
{
    use HasScenarios;

    /** @test */
    public function it_will_return_null_when_merge_is_not_allowed(): void
    {
        Merge::set(false);

        $this->assertNull($this->build('simple'));
    }
    /** @test */
    public function it_will_build_phpdoc_and_return_it_as_a_string(): void
    {
        Merge::set(true);

        foreach ($this->scenarios() as $scenario) {
            $this->assertEquals(
                $this->expected($scenario),
                $this->build($scenario)
            );
        }
    }

    private function build(string $scenario): ?string
    {
        return (new PhpdocBuilder(
            (new PhpdocResolver(
                $this->input($scenario)
            ))()
        ))();
    }

    private array $scenarios = [
        'simple' => [
            'in' => <<<EOD
            /**
             * Simpele phpdoc with a small
             * bu multiline explanation
             * 
             * @api
             * 
             * @return void
             */
            EOD,
            'out' => <<<EOD
            /**
             * Simpele phpdoc with a small
             * bu multiline explanation
             * 
             * @api
             */
            EOD
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
                EOD,
            'out' => <<<EOD
            /**
             * Apply the callback if the given "value" is (or resolves to) truthy.
             * 
             * @template TWhenParameter
             * @template TWhenReturnType
             * 
             * @param \$value Some multiline explanations will be here.
             * @param \$callback Some even longet multiline explanations will be here to be sure that it's OK.
             */
            EOD
        ]
    ];
}
