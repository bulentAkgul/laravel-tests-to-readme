<?php

namespace Bakgul\LaravelTestsToReadme\Utilities;

class GetIdentifiers
{
    public static function _(): array
    {
        return [
            'class' => config('to-readme.class_identifier'),
            'method' => config('to-readme.method_identifier'),
            'code' => config('to-readme.code_block_identifier'),
        ];
    }
}
