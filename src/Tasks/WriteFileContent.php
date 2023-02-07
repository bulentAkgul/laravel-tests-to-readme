<?php

namespace Bakgul\LaravelTestsToReadme\Tasks;

use Bakgul\LaravelHelpers\Helpers\Arr;

class WriteFileContent
{
    private ?string $sameBlock = null;
    private string $lastBlock;
    private array $block;

    public function __construct(private string $path, private array $content)
    {
    }

    public function __invoke(): void
    {
        file_put_contents($this->path, $this->content());
    }

    private function content(): string
    {
        $this->prepare();

        $this->findSameBlock();

        $this->insertBlock();

        return $this->stringify();
    }

    private function prepare(): void
    {
        $this->setLastCodeBlockKey();

        $this->setBlock();

        $this->dropBlock();
    }

    private function setLastCodeBlockKey(): void
    {
        $this->lastBlock = $this->getLastBlockKey('codeblock');
    }

    private function getLastBlockKey(string $search): string
    {
        return Arr::last(
            array_keys($this->content),
            fn ($x) => str_contains($x, $search)
        );
    }

    private function setBlock(): void
    {
        $this->block = $this->content[$this->lastBlock];
    }

    private function dropBlock(): void
    {
        Arr::forget($this->content, $this->lastBlock);
    }

    private function findSameBlock(): void
    {
        foreach ($this->content as $key => $oldBlock) {
            $this->sameBlock = $this->isBlockSame($oldBlock)
                ? $key
                : $this->sameBlock;
        }
    }

    private function isBlockSame(array $oldBlock): bool
    {
        return $this->isTestSame($oldBlock)
            && $this->isMethodSame($oldBlock);
    }

    private function isTestSame(array $oldBlock): bool
    {
        return $this->setTest($this->block)
            == $this->setTest($oldBlock);
    }

    private function setTest(array $block): ?string
    {
        return strtolower(str_replace([' ', '/', '.', '_', PHP_EOL], '', $this->getTest($block)));
    }

    private function getTest(array $block): ?string
    {
        return array_reduce(
            array_slice($block, 1, Arr::firstKey($block, '', '', '==') ?? 1 - 1),
            fn ($p, $c) => $p . $c
        );
    }

    private function isMethodSame(array $oldBlock): bool
    {
        return $this->getMethodCall($this->block)
            == $this->getMethodCall($oldBlock);
    }

    private function getMethodCall(array $block): string
    {
        return Arr::first($block, fn ($x) => trim($x) && !str_contains($x, '// '));
    }

    private function insertBlock(): void
    {
        if ($this->sameBlock) return;

        $index = $this->getNumericIndex();

        $this->content = array_slice($this->content, 0, $index + 1)
            + [$this->lastBlock => $this->block]
            + array_slice($this->content, $index + 1);
    }

    private function getNumericIndex(): int
    {
        return array_search(
            $this->getLastBlockKey("{$this->isolatePrefix()}-"),
            array_keys($this->content)
        );
    }

    private function isolatePrefix(): string
    {
        return explode('-', $this->lastBlock)[0];
    }

    private function stringify(): string
    {
        return implode(str_repeat(PHP_EOL, 2), array_map(
            fn ($x) => implode(PHP_EOL, $x),
            $this->content
        ));
    }
}
