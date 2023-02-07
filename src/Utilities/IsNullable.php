<?php

namespace Bakgul\LaravelTestsToReadme\Utilities;

use Bakgul\LaravelHelpers\Helpers\Arr;
use Bakgul\LaravelHelpers\Helpers\Str;
use ReflectionNamedType;

class IsNullable
{
    public static function _(string|ReflectionNamedType $type, array $types = []): bool
    {
        if (is_string($type)) return Str::hasSome($type, ['null', '?']);

        if ($type->allowsNull()) return true;

        return Arr::in(Arr::map($types, fn ($x) => $x->getName()), 'null');
    }
}
