<?php

namespace Bakgul\LaravelTestsToReadme\Adapters;

class PhpdocParamToAssoc
{
    public static function adapt(array $phpdoc): array
    {
        $parameters = [];

        foreach ($phpdoc as $i => $parameter) {
            $parameters[] = [
                'name' => explode(',', explode('$', $parameter[1])[1])[0],
                'position' => $i,
                'types' => PhpdocTypesToArray::adapt($parameter[0]),
                'isVariadic' => str_contains($parameter[1], '...'),
                'isReference' => str_contains($parameter[1], '&'),
                'explanations' => $parameter[2] ?? '',
            ];
        }

        return $parameters;
    }
}
