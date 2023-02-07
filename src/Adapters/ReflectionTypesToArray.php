<?php

namespace Bakgul\LaravelTestsToReadme\Adapters;

use Bakgul\LaravelTestsToReadme\Utilities\IsNullable;
use Bakgul\LaravelTestsToReadme\Utilities\SetTypes;
use ReflectionNamedType;
use ReflectionUnionType;

class ReflectionTypesToArray
{
    public static function adapt(ReflectionNamedType|ReflectionUnionType|null $types): array
    {
        $types = SetTypes::_($types);

        return array_filter(array_map(
            fn ($x) => [
                'name' => $x->getName(),
                'nullable' => IsNullable::_($x, $types),
                'builtin' => $x->isBuiltin(),
            ],
            $types
        ), fn ($x) => $x['name'] != 'null');
    }
}
