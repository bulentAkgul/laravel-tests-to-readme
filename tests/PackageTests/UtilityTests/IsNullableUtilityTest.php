<?php

namespace Bakgul\LaravelTestsToReadme\Tests\PackageTests\UtilityTests;

use Bakgul\LaravelTestsToReadme\Utilities\IsNullable;
use Bakgul\LaravelTestsToReadme\Tests\TestCase;
use Bakgul\LaravelTestsToReadme\Tests\TestTasks\InteractWithFile;
use Bakgul\LaravelTestsToReadme\Tests\TestTasks\Reflect;

class IsNullableUtilityTest extends TestCase
{
    public InteractWithFile $interact;

    /** @test */
    public function it_will_return_true_when_type_string_contains_null_or_question_mark(): void
    {
        $case = 'phpdoc';

        foreach ($this->scenarios($case) as $scenrio) {
            $this->assertEquals(
                $this->case($case, $scenrio),
                $this->isNullable($case, $scenrio)
            );
        }
    }

    /** @test */
    public function it_will_return_true_when_a_reflection_type_is_null_or_has_question_mark(): void
    {
        $case = 'reflection';

        foreach ($this->scenarios($case) as $scenrio) {
            $this->interact($scenrio);

            $this->assertEquals(
                $this->case($case, $scenrio),
                $this->isNullable($case, $scenrio, Reflect::types())
            );

            $this->interact->delete();
        }
    }

    private function scenarios($case): array
    {
        return array_keys($this->$case);
    }

    private function case(string $case, string $scenario, string $job = 'out'): bool|string
    {
        return $this->$case[$scenario][$job];
    }

    private function isNullable(string $case, string $scenario, array $types = []): bool
    {
        return IsNullable::_($this->case($case, $scenario, 'in'), $types);
    }

    private function interact(string $scenario): void
    {
        $this->interact = (new InteractWithFile([
            'content' => $this->case('reflection', $scenario, 'in'),
            'force' => true,
        ]));
    }

    public array $phpdoc = [
        'nullable_with_qm' => [
            'in' => '?array',
            'out' => true
        ],
        'nullable_with_null' => [
            'in' => 'array|string|null',
            'out' => true
        ],
        'not_nullable' => [
            'in' => 'array|string',
            'out' => false
        ]
    ];

    public array $reflection = [
        'nullable_with_qm' => [
            'in' => 'public function dummyMethod(?array $x1) {}',
            'out' => true
        ],
        'nullable_with_null' => [
            'in' => 'public function dummyMethod(array|string|null $x1) {}',
            'out' => true
        ],
        'not_nullable' => [
            'in' => 'public function dummyMethod(array|string $x1) {}',
            'out' => false
        ]
    ];
}
