<?php

namespace Bakgul\LaravelTestsToReadme\Tests\LaravelTestsToReadmeTests\UtilityTests;

use Bakgul\LaravelTestsToReadme\Tests\TestConcerns\HasInteraction;
use Bakgul\LaravelTestsToReadme\Utilities\SetTypes;
use Bakgul\LaravelTestsToReadme\Tests\TestCase;
use Bakgul\LaravelTestsToReadme\Tests\TestConcerns\HasScenarios;
use Bakgul\LaravelTestsToReadme\Tests\TestTasks\Reflect;
use ReflectionNamedType;

class SetTypesUtilityTest extends TestCase
{
    use HasInteraction, HasScenarios;

    /** @test */
    public function reflection_types_will_be_set_to_method_in_an_array(): void
    {
        foreach ($this->scenarios() as $scenario) {
            $this->file($scenario, '');

            if ($scenario == 'no_type') {
                $this->assertEquals([], $this->setTypes());
            }

            array_map(
                fn ($x) => $this->assertTrue($x instanceof ReflectionNamedType),
                $this->setTypes()
            );

            $this->deleteClass();
        }
    }

    private function setTypes(): array
    {
        return SetTypes::_(Reflect::types($this->file->className())[0]);
    }

    private array $scenarios = [
        'no_type' => 'public function dummyMethod($x1) {}',
        'single_type' => 'public function dummyMethod(array $x1) {}',
        'multi_type' => 'public function dummyMethod(array|string $x1) {}',
    ];
}
