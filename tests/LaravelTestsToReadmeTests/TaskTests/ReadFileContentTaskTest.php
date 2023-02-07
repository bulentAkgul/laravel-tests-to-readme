<?php

namespace Bakgul\LaravelTestsToReadme\Tests\LaravelTestsToReadmeTests\TaskTests;

use Bakgul\LaravelTestsToReadme\Tasks\ReadFileContent;
use Bakgul\LaravelTestsToReadme\Tests\TestCase;
use Bakgul\LaravelTestsToReadme\Tests\TestConcerns\HasInteraction;
use Bakgul\LaravelTestsToReadme\Tests\TestConcerns\HasScenarios;

class ReadFileContentTaskTest extends TestCase
{
    /** @test */
    public function readme_files_content_will_be_converted_an_array_of_blocks(): void
    {
        $this->assertEquals(
            [
                [
                    "## \App\Helpers\TestClass",
                ],
                [
                    "this is a block of text that contains unordered list",
                    "",
                    "- list item 1",
                    "- list item 2",
                    "- list item 3",
                ],
                [
                    "### xxx",
                ],
                [
                    "This is a block to explain method behaviour",
                ],
                [
                    "```php",
                    "/**",
                    " * Does xxx stuff.",
                    " */",
                    "public static function xxx(string \$x1, string \$x2): array",
                    "```",
                ],
                [
                    "This is another text block (1) to explain code execution.",
                    "",
                    "This is another text block (2) to explain code execution.",
                    "",
                    "This is another text block (3) to explain code execution.",
                ],
                [
                    "```php",
                    "// maybe another message",
                    "",
                    "TestClass::xxx('ss', 'aa');",
                    "",
                    "// ['ss', 'aa']",
                    "```",
                ],
                [
                    "This is another text block (4) to explain code execution.",
                ],
                [
                    "```php",
                    "// maybe another message",
                    "",
                    "TestClass::xxx('zzz', 'ss');",
                    "",
                    "// ['zzz', 'ss']",
                    "```",
                ],
                [
                    "End of file...",
                ],
            ],
            array_map(
                fn ($x) => array_map(fn ($y) => trim($y, PHP_EOL), $x),
                array_values((new ReadFileContent(
                    __DIR__ . '/../../TestFiles/TestClass.md'
                ))())
            )
        );
    }
}
