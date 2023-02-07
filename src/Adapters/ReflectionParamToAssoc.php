<?php

namespace Bakgul\LaravelTestsToReadme\Adapters;

class ReflectionParamToAssoc
{
    public static function adapt(array $parameters): array
    {
        return array_map(fn ($x) => [
            'name' => $x->getName(),
            'position' => $x->getPosition(),
            'types' => ReflectionTypesToArray::adapt($x->getType()),
            'hasDefault' => $d = $x->isDefaultValueAvailable(),
            'default' => $d ? $x->getDefaultValue() : null,
            'isVariadic' => $x->isVariadic(),
            'isReference' => $x->isPassedByReference(),
        ], $parameters);
    }
}
