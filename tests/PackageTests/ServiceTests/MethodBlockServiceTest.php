<?php

namespace Bakgul\LaravelTestsToReadme\Tests\PackageTests\ServiceTests;

use Bakgul\LaravelTestsToReadme\Helpers\Merge;
use Bakgul\LaravelTestsToReadme\Services\MethodBlockService;
use Bakgul\LaravelTestsToReadme\Tests\TestCase;
use Bakgul\LaravelTestsToReadme\Tests\TestConcerns\HasInteraction;
use Bakgul\LaravelTestsToReadme\Tests\TestConcerns\HasScenarios;
use Bakgul\LaravelTestsToReadme\Tests\TestTasks\Reflect;

class MethodBlockServiceTest extends TestCase
{
    use HasInteraction, HasScenarios;

    /** @test */
    public function it_will_produce_method_block_by_merging_phpdoc_to_typehint(): void
    {
        foreach ($this->scenarios() as $scenario) {
            Merge::set(!str_contains($scenario, 'no_merge'));

            $this->file($scenario);

            $this->assertEquals(
                $this->expected($scenario),
                $this->block(),
            );

            $this->deleteClass();
        }
    }

    private function block(): array
    {
        return (new MethodBlockService(
            Reflect::method($this->file->className())
        ))();
    }

    private array $scenarios = [
        'simple' => [
            'in' => <<<EOD
            /**
             * Method explanation on Phpdoc.
             * 
             * @param  string  \$x
             * @return array
             */
            public static function dummyMethod(\$x) {}
            EOD,
            'out' => [
                <<<EOD
                /**
                 * Method explanation on Phpdoc.
                 */
                EOD,
                'public static function dummyMethod(string $x): array'
            ]
        ],
        'has_warning' => [
            'in' => <<<EOD
            /**
             * Method explanation on Phpdoc.
             * 
             * @param string \$x
             * @return array
             */
            public static function dummyMethod(array \$x) {}
            EOD,
            'out' => [
                <<<EOD
                WARNINGS:
                - mismatched types\n
                EOD,
                <<<EOD
                /**
                 * Method explanation on Phpdoc.
                 */
                EOD,
                'public static function dummyMethod(array $x): array'
            ]
        ],
        'has_multiple_warnings' => [
            'in' => <<<EOD
                /**
                 * Method explanation on Phpdoc.
                 * 
                 * @param string \$x
                 * @param array \$y
                 * @return array
                 */
                public static function dummyMethod(array \$x, array|null \$y) {}
                EOD,
            'out' => [
                <<<EOD
                WARNINGS:
                - mismatched types
                - missmatched nullable types\n
                EOD,
                <<<EOD
                /**
                 * Method explanation on Phpdoc.
                 */
                EOD,
                'public static function dummyMethod(array $x, ?array $y): array'
            ]
        ],
        'no_merge_has_multiple_warnings' => [
            'in' => <<<EOD
                    /**
                     * Method explanation on Phpdoc.
                     * 
                     * @param string \$x
                     * @param array \$y
                     * @return array
                     */
                    public static function dummyMethod(array \$x, array|null \$y) {}
                    EOD,
            'out' => [
                <<<EOD
                    WARNINGS:
                    - mismatched types
                    - missmatched nullable types\n
                    EOD,
                <<<EOD
                    /**
                     * Method explanation on Phpdoc.
                     * 
                     * @param string \$x
                     * @param array \$y
                     * @return array
                     */
                    EOD,
                'public static function dummyMethod(array $x, ?array $y)'
            ]
        ],
    ];
}
