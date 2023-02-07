<?php

namespace Bakgul\LaravelTestsToReadme\Utilities;

class SetTitle
{
    public static function _(string $type, string $title): string
    {
        return config("to-readme.{$type}_identifier") . $title;
    }
}
