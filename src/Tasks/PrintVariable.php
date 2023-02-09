<?php

namespace Bakgul\LaravelTestsToReadme\Tasks;

use Bakgul\LaravelHelpers\Helpers\Str;

class PrintVariable
{
    public static function _(mixed $input): string
    {
        return match (true) {
            $input === '' => "''",
            is_null($input) => 'null',
            is_numeric($input) => (float) $input,
            is_bool($input) => $input ? 'true' : 'false',
            is_array($input) => self::prettyPrint($input),
            is_object($input) => get_class($input),
            default => Str::enclose((string) $input, 'sq')
        };
    }

    private static function prettyPrint(array $input): string
    {
        return str_replace(',', ', ', str_replace(
            ['"', '{', '}', ':', ', ', '\\/'],
            ["'", '[', ']', ' => ', ',', '/'],
            json_encode($input)
        ));
    }
}
