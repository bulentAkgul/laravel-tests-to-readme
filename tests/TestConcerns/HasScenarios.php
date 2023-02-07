<?php

namespace Bakgul\LaravelTestsToReadme\Tests\TestConcerns;

use Bakgul\LaravelHelpers\Helpers\Arr;
use Bakgul\LaravelHelpers\Helpers\Str;

trait HasScenarios
{
    private function scenarios(string $case = ''): array
    {
        return array_keys($case ? $this->scenarios[$case] : $this->scenarios);
    }

    private function scenario(string $key, string $job): array|string
    {
        return Arr::get($this->scenarios, Str::append($key, $job, '.'));
    }

    private function input(string $key, string $job = 'in'): array|string
    {
        return $key ? $this->scenario($key, $job) : '';
    }

    private function expected(string $key, string $job = 'out'): array|string
    {
        return $this->scenario($key, $job);
    }
}
