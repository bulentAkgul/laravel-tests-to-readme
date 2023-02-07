<?php

namespace Bakgul\LaravelTestsToReadme\Tasks;

use Bakgul\LaravelHelpers\Helpers\Folder;
use Bakgul\LaravelHelpers\Helpers\Path;
use Bakgul\LaravelHelpers\Helpers\Str;

class GetClosestReadme
{
    public static function handle(string $path)
    {
        $hasFound = false;

        if (self::isFound($path))
            return self::path($path);

        while (!$hasFound) {
            $path = Str::dropTail($path);

            if (!$path) return null;

            $hasFound = Folder::contains($path, config('to-readme.output_folder'));
        }

        return self::path($path);
    }

    private static function isFound(string $path): bool
    {
        return Folder::contains($path, config('to-readme.output_folder'));
    }

    private static function path(string $path): string
    {
        return Path::glue([$path, config('to-readme.output_folder')]);
    }
}
