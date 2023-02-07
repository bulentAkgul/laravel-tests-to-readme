<?php

namespace Bakgul\LaravelTestsToReadme\Adapters;

use Bakgul\LaravelTestsToReadme\Utilities\IsNullable;

class PhpdocTypesToArray
{
    public static function adapt(string $types)
    {
        return array_filter(array_map(
            fn ($x) => [
                'name' => str_replace('?', '', $x),
                'nullable' => IsNullable::_($types),
                'builtin' => null
            ],
            explode('|', $types)
        ), fn ($x) => $x['name'] != 'null');
    }
}
