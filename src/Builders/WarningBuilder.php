<?php

namespace Bakgul\LaravelTestsToReadme\Builders;

use Bakgul\LaravelHelpers\Helpers\Arr;

class WarningBuilder
{
    public function __invoke(array $parameters): string
    {
        $warnings = Arr::unique(Arr::pluck($parameters, 'warning'));

        return $warnings ? implode(PHP_EOL, [
            'WARNINGS:',
            ...array_map(fn ($x) => "- {$x}", $warnings),
            ''
        ]) : '';
    }
}
