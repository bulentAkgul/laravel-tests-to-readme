<?php

namespace Bakgul\LaravelTestsToReadme\Tests\LaravelTestsToReadmeTests\BuilderTests;

use Bakgul\LaravelTestsToReadme\Builders\MethodBuilder;
use Bakgul\LaravelTestsToReadme\Helpers\Merge;
use Bakgul\LaravelTestsToReadme\Resolvers\ParameterResolver;
use Bakgul\LaravelTestsToReadme\Resolvers\PhpdocResolver;
use Bakgul\LaravelTestsToReadme\Resolvers\ReturnTypeResolver;
use Bakgul\LaravelTestsToReadme\Tests\TestCase;
use Bakgul\LaravelTestsToReadme\Tests\TestConcerns\HasInteraction;
use Bakgul\LaravelTestsToReadme\Tests\TestConcerns\HasScenarios;
use Bakgul\LaravelTestsToReadme\Tests\TestTasks\Reflect;
use ReflectionMethod;

class MethodBuilderTest extends TestCase
{
    use HasInteraction, HasScenarios;

    /** @test */
    public function it_will_build_method_definition_by_merging_with_phpdoc_when_necessary(): void
    {
        foreach (array_keys($this->scenarios) as $case) {
            Merge::set($case == 'mergable');

            foreach ($this->scenarios($case) as $scenario) {
                $scenario = "{$case}.{$scenario}";

                $this->file($scenario);

                $this->assertEquals(
                    $this->expected($scenario),
                    $this->build($scenario)
                );

                $this->deleteClass();
            }
        }
    }

    private function build(string $scenario): string
    {
        $phpdoc = $this->phpdoc($scenario);

        return (new MethodBuilder(
            $m = Reflect::method($this->file->className()),
            $this->parameters($m, $phpdoc),
            $this->returns($m, $phpdoc)
        ))();
    }

    private function phpdoc(string $scenario): array|string
    {
        return (new PhpdocResolver($this->input($scenario)))();
    }

    private function parameters(ReflectionMethod $method, array $phpdoc): array
    {
        return (new ParameterResolver(
            $method->getParameters(),
            $phpdoc['param'] ?? []
        ))();
    }

    private function returns(ReflectionMethod $method, array $phpdoc): array
    {
        return (new ReturnTypeResolver(
            $method->getReturnType(),
            $phpdoc['return'] ?? []
        ))();
    }

    private array $scenarios = [
        'mergable' => [
            'simple' => [
                'in' => <<<EOD
                    /**
                     * This is a simple block
                     * 
                     * @param array \$value
                     * @return array
                     */
                    public function dummyMethod(array \$value): array { return []; }
                    EOD,
                'out' => 'public function dummyMethod(array $value): array'
            ],
            'merged' => [
                'in' => <<<EOD
                    /**
                     * This is a simple block
                     * 
                     * @param array \$value
                     * @return array
                     */
                    public function dummyMethod(\$value) { return []; }
                    EOD,
                'out' => 'public function dummyMethod(array $value): array'
            ],
        ],
        'not_mergable' => [
            'simple' => [
                'in' => <<<EOD
                    /**
                     * This is a simple block
                     * 
                     * @param array \$value
                     * @return array
                     */
                    public function dummyMethod(array \$value): array { return []; }
                    EOD,
                'out' => 'public function dummyMethod(array $value): array'
            ],
            'not_merged' => [
                'in' => <<<EOD
                    /**
                     * This is a simple block
                     * 
                     * @param array \$value
                     * @return array
                     */
                    public static function dummyMethod(\$value) { return []; }
                    EOD,
                'out' => 'public static function dummyMethod($value)'
            ],
        ],
        'multiline' => [
            'too_many_params' => [
                'in' => 'public function dummyMethod(array $value, string $string, \App\Models\User $user, \App\Models\Post $post): array { return []; }',
                'out' => <<< EOD
                public function dummyMethod(
                    array \$value,
                    string \$string,
                    App\Models\User \$user,
                    App\Models\Post \$post,
                ): array
                EOD
            ]
        ]
    ];
}
