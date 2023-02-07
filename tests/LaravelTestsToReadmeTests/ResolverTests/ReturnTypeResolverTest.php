<?php

namespace Bakgul\LaravelTestsToReadme\Tests\LaravelTestsToReadmeTests\ResolverTests;

use Bakgul\LaravelTestsToReadme\Helpers\Merge;
use Bakgul\LaravelTestsToReadme\Resolvers\ReturnTypeResolver;
use Bakgul\LaravelTestsToReadme\Tests\TestCase;
use Bakgul\LaravelTestsToReadme\Tests\TestConcerns\HasInteraction;
use Bakgul\LaravelTestsToReadme\Tests\TestConcerns\HasScenarios;
use Bakgul\LaravelTestsToReadme\Tests\TestTasks\Reflect;

class ReturnTypeResolverTest extends TestCase
{
    use HasInteraction, HasScenarios;

    /** @test */
    public function return_types_on_phpdoc_will_return_when_it_is_mergable_and_typehint_has_no_return_type(): void
    {
        Merge::set(true);

        foreach ($this->scenarios('phpdoc') as $scenario) {
            $this->assertScenario("phpdoc.{$scenario}", false);
        }
    }

    /** @test */
    public function empty_array_will_return_when_it_is_not_mergable_and_typehint_has_no_return_type(): void
    {
        Merge::set(false);

        $this->assertEquals(
            $this->scenario('typehint.no_type', 'out'),
            $this->resolve(false, $this->getTypes('phpdoc.one_type')),
        );
    }

    /** @test */
    public function return_types_on_type_hints_will_be_converted_to_array(): void
    {
        foreach ($this->scenarios('typehint') as $scenario) {
            $key = "typehint.{$scenario}";

            $this->file($key);

            $this->assertScenario($key, true);

            $this->deleteClass();
        }
    }

    private function assertScenario(string $key, bool $class): void
    {
        $this->assertEquals(
            $this->expected($key),
            $this->resolve($class, $this->getTypes($key)),
            $key
        );
    }

    private function resolve(bool $class, array $phpdoc): array
    {
        return (new ReturnTypeResolver(
            $class ? Reflect::return($this->file->className()) : null,
            $phpdoc
        ))();
    }

    private function getTypes(string $scenario): array
    {
        return array_filter(explode('|', trim(
            explode('@return', explode('*/', $this->input($scenario))[0])[1] ?? ''
        )));
    }

    private array $scenarios = [
        'phpdoc' => [
            'no_type' => [
                'in' => "/** \n * \n */ \n public function dummyMethod() {}",
                'out' => ['types' => [], 'warning' => '']
            ],
            'one_type' => [
                'in' => "/** \n * @return array \n */ \n public function dummyMethod() {}",
                'out' => ['types' => ['array'], 'warning' => '']
            ],
            'multi_type' => [
                'in' => "/** \n * @return array|string \n */ \n public function dummyMethod() {}",
                'out' => ['types' => ['array', 'string'], 'warning' => '']
            ],
            'nullable_type' => [
                'in' => "/** \n * @return array|null \n */ \n public function dummyMethod() {}",
                'out' => ['types' => ['array', 'null'], 'warning' => '']
            ],
            'nullable_with_qm_type' => [
                'in' => "/** \n * @return ?array \n */ \n public function dummyMethod() {}",
                'out' => ['types' => ['array', 'null'], 'warning' => '']
            ],
        ],
        'typehint' => [
            'no_type' => [
                'in' => "public function dummyMethod() {}",
                'out' => ['types' => [], 'warning' => '']
            ],
            'one_type' => [
                'in' => "public function dummyMethod(): array {}",
                'out' => ['types' => ['array'], 'warning' => '']
            ],
            'multi_type' => [
                'in' => "public function dummyMethod(): array|string {}",
                'out' => ['types' => ['array', 'string'], 'warning' => '']
            ],
            'nullable_type' => [
                'in' => "public function dummyMethod(): array|null {}",
                'out' => ['types' => ['array', 'null'], 'warning' => '']
            ],
            'nullable_with_qm_type' => [
                'in' => "public function dummyMethod(): ?array {}",
                'out' => ['types' => ['array', 'null'], 'warning' => '']
            ],
        ],
    ];
}
