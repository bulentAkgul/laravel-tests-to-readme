<?php

namespace Bakgul\LaravelTestsToReadme\Tests\LaravelTestsToReadmeTests\ServiceTests;

use Bakgul\LaravelTestsToReadme\Services\ReadmeContentService;
use Bakgul\LaravelTestsToReadme\Tests\TestCase;
use Bakgul\LaravelTestsToReadme\Tests\TestConcerns\HasInteraction;
use Bakgul\LaravelTestsToReadme\Tests\TestConcerns\HasScenarios;

class ReadmeContentServiceTest extends TestCase
{
    use HasInteraction, HasScenarios;

    const PATH = __DIR__ . '/../../TestFiles/TestClass.php';

    /** @test */
    public function itit_will_generate_bare_minimum_readme_structure(): void
    {
        $this->file('', '', 'TestCalls');

        $this->file->makeReadmeDir();

        $this->assertEquals(
            ['TestClass' => ['## TestClass']],
            (new ReadmeContentService(
                self::PATH
            ))->init(['class' => 'TestClass'])
        );
    }

    /** @test */
    public function read_will_read_the_readme_file_content(): void
    {
        $this->file('', '', 'TestCalls');

        $this->file->copyReadme();

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
            array_values((new ReadmeContentService(self::PATH))->read())
        );
    }

    /** @test */
    public function write_will_put_readme_content_to_the_target_file(): void
    {
        $this->file('', '', 'TestCalls');

        $this->file->makeReadmeDir();

        ((new ReadmeContentService($this->file->fullPath()))->write([
            '\App\Helpers\Folder' => ['## \App\Helpers\Folder'],
            'content' => ['### content'],
            'content-codeblock-185-952-874' => [
                '```php',
                '/**',
                ' * This comes from phpdoc.',
                ' */',
                'public static function content(string $path): array',
                '```'
            ],
            'content-codeblock-455-724-367' => [
                '```php',
                '// This is the name of test method - 1',
                '',
                "Folder::content(base_path('Aaa'));",
                '',
                "// ['Bbb.php', 'Ccc', 'Ddd']",
                '```'
            ],
        ]));

        $this->assertEquals(
            <<<EOD
            ## \App\Helpers\Folder

            ### content

            ```php
            /**
             * This comes from phpdoc.
             */
            public static function content(string \$path): array
            ```

            ```php
            // This is the name of test method - 1

            Folder::content(base_path('Aaa'));

            // ['Bbb.php', 'Ccc', 'Ddd']
            ```
            EOD,
            $this->file->readme()
        );
    }

    public function tearDown(): void
    {
        $this->file->deleteReadme();

        $this->deleteClass();

        parent::tearDown();
    }
}
