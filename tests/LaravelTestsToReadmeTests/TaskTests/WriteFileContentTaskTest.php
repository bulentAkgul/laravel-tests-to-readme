<?php

namespace Bakgul\LaravelTestsToReadme\Tests\LaravelTestsToReadmeTests\TaskTests;

use Bakgul\LaravelTestsToReadme\Tasks\WriteFileContent;
use Bakgul\LaravelTestsToReadme\Tests\TestCase;
use Bakgul\LaravelTestsToReadme\Tests\TestConcerns\HasScenarios;

class WriteFileContentTaskTest extends TestCase
{
    use HasScenarios;

    const PATH = __DIR__ . '/../../TestFiles/Folder.md';

    /** @test */
    public function a_readme_file_will_be_created_first_time_test_execution(): void
    {
        foreach ($this->scenarios() as $scenario) {
            if (file_exists(self::PATH)) unlink(self::PATH);

            $this->write($scenario);

            $this->assertContent($scenario);
        }

        unlink(self::PATH);
    }

    private function write(string $scenario): void
    {
        (new WriteFileContent(
            self::PATH,
            $this->scenario($scenario, 'put')
        ))();
    }

    private function assertContent(string $scenario): void
    {
        $this->assertEquals(
            $this->scenario($scenario, 'get'),
            file_get_contents(self::PATH),
            $scenario
        );
    }

    private array $scenarios = [
        'init' => [
            'put' => [
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
            ],
            'get' => <<<EOD
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
            EOD
        ],
        'add_new_code' => [
            'put' => [
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
                'content-codeblock-223-454-454' => [
                    '```php',
                    '// This is the name of test method - 2',
                    '',
                    "Folder::content(base_path('Xxx'));",
                    '',
                    "// ['Yyy.php', 'Zzz.php']",
                    '```'
                ],
            ],
            'get' => <<<EOD
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

            ```php
            // This is the name of test method - 2

            Folder::content(base_path('Xxx'));

            // ['Yyy.php', 'Zzz.php']
            ```
            EOD
        ],
        'add_new_method' => [
            'put' => [
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
                'content-codeblock-223-454-454' => [
                    '```php',
                    '// This is the name of test method - 2',
                    '',
                    "Folder::content(base_path('Xxx'));",
                    '',
                    "// ['Yyy.php', 'Zzz.php']",
                    '```'
                ],
                'list' => ['### list'],
                'list-codeblock-185-952-874' => [
                    '```php',
                    '/**',
                    ' * This comes from phpdoc.',
                    ' */',
                    'public static function list(string $path): array',
                    '```'
                ],
                'list-codeblock-455-724-367' => [
                    '```php',
                    '// This is the name of test method - 3',
                    '',
                    "Folder::list(base_path('Aaa'));",
                    '',
                    "// ['Bbb.php', 'Ccc', 'Ddd']",
                    '```'
                ],
            ],
            'get' => <<<EOD
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

            ```php
            // This is the name of test method - 2

            Folder::content(base_path('Xxx'));

            // ['Yyy.php', 'Zzz.php']
            ```

            ### list

            ```php
            /**
             * This comes from phpdoc.
             */
            public static function list(string \$path): array
            ```

            ```php
            // This is the name of test method - 3

            Folder::list(base_path('Aaa'));

            // ['Bbb.php', 'Ccc', 'Ddd']
            ```
            EOD
        ],
        'add_new_code_to_the_first_method' => [
            'put' => [
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
                'content-codeblock-223-454-454' => [
                    '```php',
                    '// This is the name of test method - 2',
                    '',
                    "Folder::content(base_path('Xxx'));",
                    '',
                    "// ['Yyy.php', 'Zzz.php']",
                    '```'
                ],
                'list' => ['### list'],
                'list-codeblock-185-952-874' => [
                    '```php',
                    '/**',
                    ' * This comes from phpdoc.',
                    ' */',
                    'public static function list(string $path): array',
                    '```'
                ],
                'list-codeblock-455-724-367' => [
                    '```php',
                    '// This is the name of test method - 3',
                    '',
                    "Folder::list(base_path('Aaa'));",
                    '',
                    "// ['Bbb.php', 'Ccc', 'Ddd']",
                    '```'
                ],
                'content-codeblock-111-222-333' => [
                    '```php',
                    '// This is the name of test method - 4',
                    '',
                    "Folder::content(base_path('Kkk'));",
                    '',
                    "// ['Mmm.php', 'Nnn.php']",
                    '```'
                ],
            ],
            'get' => <<<EOD
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

            ```php
            // This is the name of test method - 2

            Folder::content(base_path('Xxx'));

            // ['Yyy.php', 'Zzz.php']
            ```

            ```php
            // This is the name of test method - 4

            Folder::content(base_path('Kkk'));

            // ['Mmm.php', 'Nnn.php']
            ```

            ### list

            ```php
            /**
             * This comes from phpdoc.
             */
            public static function list(string \$path): array
            ```

            ```php
            // This is the name of test method - 3

            Folder::list(base_path('Aaa'));

            // ['Bbb.php', 'Ccc', 'Ddd']
            ```
            EOD
        ],
        'code_blocks_immutable' => [
            'put' => [
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
                'content-codeblock-223-454-454' => [
                    '```php',
                    '// This is the name of test method - 2',
                    '',
                    "Folder::content(base_path('Xxx'));",
                    '',
                    "// ['Yyy.php', 'Zzz.php']",
                    '```'
                ],
                'list' => ['### list'],
                'list-codeblock-185-952-874' => [
                    '```php',
                    '/**',
                    ' * This comes from phpdoc.',
                    ' */',
                    'public static function list(string $path): array',
                    '```'
                ],
                'list-codeblock-455-724-367' => [
                    '```php',
                    '// This is the name of test method - 3',
                    '',
                    "Folder::list(base_path('Aaa'));",
                    '',
                    "// ['Bbb.php', 'Ccc', 'Ddd']",
                    '```'
                ],
                'content-codeblock-111-222-333' => [
                    '```php',
                    '// This is the name of test method - 2',
                    '',
                    '// This is the new version but will not be printed out',
                    '',
                    "Folder::content(base_path('111'));",
                    '',
                    "// ['222.php', '333.php']",
                    '```'
                ],
            ],
            'get' => <<<EOD
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

            ```php
            // This is the name of test method - 2

            Folder::content(base_path('Xxx'));

            // ['Yyy.php', 'Zzz.php']
            ```

            ### list

            ```php
            /**
             * This comes from phpdoc.
             */
            public static function list(string \$path): array
            ```

            ```php
            // This is the name of test method - 3

            Folder::list(base_path('Aaa'));

            // ['Bbb.php', 'Ccc', 'Ddd']
            ```
            EOD
        ],
        'add_a_code_block_to_the_first_method_when_file_has_text_blocks' => [
            'put' => [
                '\App\Helpers\Folder' => ['## \App\Helpers\Folder'],
                'content' => ['### content'],
                'content-textblock-111-111-111' => [
                    'paragraph 1',
                    '',
                    '- list item 1',
                    '- list item 2',
                ],
                'content-codeblock-185-952-874' => [
                    '```php',
                    '/**',
                    ' * This comes from phpdoc.',
                    ' */',
                    'public static function content(string $path): array',
                    '```'
                ],
                'content-textblock-222-222-222' => [
                    'paragraph 3',
                    '',
                    'paragraph 4',
                    '',
                    '| col 1 | col 2 | col 3 |',
                    '| --- | --- |',
                    '| row 1.1 | row 1.2 | row 1.3 |',
                    '| row 2.1 | row 2.2 | row 2.3 |',
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
                'content-textblock-333-333-333' => [
                    'paragraph 5',
                    '',
                    'paragraph 6',
                    '',
                    'paragraph 7',
                ],
                'content-codeblock-223-454-454' => [
                    '```php',
                    '// This is the name of test method - 2',
                    '',
                    "Folder::content(base_path('Xxx'));",
                    '',
                    "// ['Yyy.php', 'Zzz.php']",
                    '```'
                ],
                'list' => ['### list'],
                'list-codeblock-185-952-874' => [
                    '```php',
                    '/**',
                    ' * This comes from phpdoc.',
                    ' */',
                    'public static function list(string $path): array',
                    '```'
                ],
                'list-textblock-444-444-444' => [
                    'paragraph 8',
                    '',
                    'paragraph 9',
                ],
                'list-codeblock-455-724-367' => [
                    '```php',
                    '// This is the name of test method - 3',
                    '',
                    "Folder::list(base_path('Aaa'));",
                    '',
                    "// ['Bbb.php', 'Ccc', 'Ddd']",
                    '```'
                ],
                'content-codeblock-111-222-333' => [
                    '```php',
                    '// This is the name of test method - 4',
                    '',
                    '// This added to file.',
                    '',
                    "Folder::content(base_path('Kkk'));",
                    '',
                    "// ['Mmm.php', 'Nnn.php']",
                    '```'
                ],
            ],
            'get' => <<<EOD
            ## \App\Helpers\Folder

            ### content

            paragraph 1

            - list item 1
            - list item 2

            ```php
            /**
             * This comes from phpdoc.
             */
            public static function content(string \$path): array
            ```

            paragraph 3

            paragraph 4

            | col 1 | col 2 | col 3 |
            | --- | --- |
            | row 1.1 | row 1.2 | row 1.3 |
            | row 2.1 | row 2.2 | row 2.3 |

            ```php
            // This is the name of test method - 1

            Folder::content(base_path('Aaa'));

            // ['Bbb.php', 'Ccc', 'Ddd']
            ```

            paragraph 5

            paragraph 6

            paragraph 7

            ```php
            // This is the name of test method - 2

            Folder::content(base_path('Xxx'));

            // ['Yyy.php', 'Zzz.php']
            ```

            ```php
            // This is the name of test method - 4

            // This added to file.

            Folder::content(base_path('Kkk'));

            // ['Mmm.php', 'Nnn.php']
            ```

            ### list

            ```php
            /**
             * This comes from phpdoc.
             */
            public static function list(string \$path): array
            ```

            paragraph 8

            paragraph 9

            ```php
            // This is the name of test method - 3

            Folder::list(base_path('Aaa'));

            // ['Bbb.php', 'Ccc', 'Ddd']
            ```
            EOD
        ],
    ];
}
