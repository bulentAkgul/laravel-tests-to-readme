<?php

namespace Bakgul\LaravelTestsToReadme\Helpers;

class Merge
{
    public static function true(): bool
    {
        return config('to-readme.merge_phpdoc_and_method_declaration');
    }

    public static function false(): bool
    {
        return !self::true();
    }

    public static function set(bool $value): void
    {
        config()->set('to-readme.merge_phpdoc_and_method_declaration', $value);
    }
}
