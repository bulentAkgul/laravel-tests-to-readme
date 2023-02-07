<?php

namespace Bakgul\LaravelTestsToReadme\Utilities;

use ReflectionNamedType;
use ReflectionUnionType;

class SetTypes
{
    public static function _(ReflectionNamedType|ReflectionUnionType|null $types): array
    {
        return match (true) {
            $types instanceof ReflectionUnionType => $types->getTypes(),
            $types instanceof ReflectionNamedType => [$types],
            default => []
        };
    }
}
