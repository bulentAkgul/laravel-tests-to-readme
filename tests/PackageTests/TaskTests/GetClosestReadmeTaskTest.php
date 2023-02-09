<?php

namespace Bakgul\LaravelTestsToReadme\Tests\PackageTests\TaskTests;

use Bakgul\LaravelHelpers\Helpers\Folder;
use Bakgul\LaravelTestsToReadme\Tasks\GetClosestReadme;
use Bakgul\LaravelTestsToReadme\Tests\TestCase;

class GetClosestReadmeTaskTest extends TestCase
{
    /** @test */
    public function it_will_return_the_path_of_the_closest_readme_folder_to_given_path(): void
    {
        $path = __DIR__ . "/../../" . config('to-readme.output_folder');
        $file = "{$path}/Arr.md";

        mkdir($path);
        file_put_contents($file, '');

        $this->assertEquals(
            Folder::content($path),
            Folder::content(GetClosestReadme::handle(__DIR__))
        );

        unlink($file);
        rmdir($path);
    }
}
