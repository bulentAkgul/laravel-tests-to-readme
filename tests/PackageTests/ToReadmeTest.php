<?php

namespace Bakgul\LaravelTestsToReadme\Tests\PackageTests;

use Bakgul\LaravelTestsToReadme\Tests\TestCase;
use Bakgul\LaravelTestsToReadme\Tests\TestConcerns\HasInteraction;
use Bakgul\LaravelTestsToReadme\Tests\TestConcerns\HasScenarios;
use Bakgul\LaravelTestsToReadme\ToReadme;

class ToReadmeTest extends TestCase
{
    use HasInteraction, HasScenarios;

    /** @test */
    public function it_will_create_readme(): void
    {
        $this->file('init', class: 'TestClass');

        $this->file->makeReadmeDir();

        $this->callToReadme([1, 2]);

        $this->assertEquals(
            $this->expected('init'),
            $this->file->readme()
        );
    }

    /** @test */
    public function it_will_extend_readme(): void
    {
        $this->file('init', class: 'TestClass');

        $this->file->copyReadme();

        $this->callToReadme([1, 2]);

        $this->assertEquals(
            $this->expected('extend'),
            $this->file->readme()
        );
    }

    private function callToReadme(array $args): void
    {
        (new ToReadme([
            'test' => 'it_will_convert_int_to_array',
            'class' => $this->file->class(),
            'method' => 'dummyMethod',
            'args' => $args,
            'message' => 'something to write',
        ]))->write();
    }

    public function tearDown(): void
    {
        $this->file->deleteReadme();

        $this->deleteClass();

        parent::tearDown();
    }

    private array $scenarios = [
        'init' => [
            'in' => 'public static function dummyMethod(int $x, int $y): array { return [$x, $y]; }',
            'out' => <<<EOD
            ## TestClass

            ### dummyMethod

            ```php
            /**
             */
            public static function dummyMethod(int \$x, int \$y): array
            ```

            ```php
            // It will convert int to array.

            // something to write

            TestClass::dummyMethod(1, 2);

            // [1, 2]
            ```
            EOD
        ],
        'extend' => [
            'in' => 'public static function dummyMethod(int $x, int $y): array { return [$x, $y]; }',
            'out' => <<<EOD
            ## \App\Helpers\TestClass

            this is a block of text that contains unordered list

            - list item 1
            - list item 2
            - list item 3

            ### xxx

            This is a block to explain method behaviour

            ```php
            /**
             * Does xxx stuff.
             */
            public static function xxx(string \$x1, string \$x2): array
            ```

            This is another text block (1) to explain code execution.

            This is another text block (2) to explain code execution.

            This is another text block (3) to explain code execution.

            ```php
            // maybe another message

            TestClass::xxx('ss', 'aa');

            // ['ss', 'aa']
            ```

            This is another text block (4) to explain code execution.

            ```php
            // maybe another message

            TestClass::xxx('zzz', 'ss');

            // ['zzz', 'ss']
            ```

            End of file...

            ### dummyMethod

            ```php
            /**
             */
            public static function dummyMethod(int \$x, int \$y): array
            ```

            ```php
            // It will convert int to array.

            // something to write

            TestClass::dummyMethod(1, 2);

            // [1, 2]
            ```
            EOD
        ],
    ];
}
