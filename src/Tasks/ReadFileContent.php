<?php

namespace Bakgul\LaravelTestsToReadme\Tasks;

use Bakgul\LaravelHelpers\Helpers\Arr;
use Bakgul\LaravelHelpers\Helpers\Str;
use Bakgul\LaravelTestsToReadme\Utilities\GetIdentifiers;

class ReadFileContent
{
    private array $content = [];
    private array $identifiers;
    private string $currentBlock;
    private string $line;

    public function __construct(private string $path)
    {
        $this->setIdentifiers();
    }

    private function setIdentifiers(): void
    {
        $this->identifiers = GetIdentifiers::_();
    }

    public function __invoke(): array
    {
        foreach ($this->read() as $line) {
            $this->setLine($line);

            $this->handleCurrentBlock();

            $this->addLine();

            $this->unsetCurrentBlock();
        }

        $this->fixTextBlocks();

        return $this->content;
    }

    public function read(): array
    {
        return file_exists($this->path) ? file($this->path) : [];
    }

    private function setLine(string $line): void
    {
        $this->line = trim($line, PHP_EOL);
    }

    private function handleCurrentBlock()
    {
        $key = $this->generateKey();

        if (!$key) {
            $this->closeCurrentBlock();

            return;
        }

        $this->currentBlock = $key;

        $this->content[$key] = [];
    }

    private function generateKey(): string
    {
        return (new SetBlockKey(
            $this->line,
            $this->currentBlock ?? '',
            $this->identifiers
        ))();
    }

    private function closeCurrentBlock(): void
    {
        $this->currentBlock = $this->isCodeCloser() ? '' : $this->currentBlock;
    }

    private function isCodeCloser(): bool
    {
        return trim($this->line) == '```';
    }

    private function addLine(): void
    {
        $this->content[$this->getBlockKey()][] = $this->line;
    }

    private function getBlockKey(): string
    {
        return $this->currentBlock ?: array_key_last($this->content);
    }

    private function unsetCurrentBlock(): void
    {
        $this->currentBlock = $this->isTitleBlock() ? '' : $this->currentBlock;
    }

    private function isTitleBlock(): bool
    {
        return Str::contains($this->line, [
            $this->identifiers['class'],
            $this->identifiers['method']
        ]);
    }

    private function fixTextBlocks(): void
    {
        foreach ($this->content as $key => $block) {
            if ($this->isNotText($key)) continue;

            $block = Arr::where($block, fn ($x) => trim($x));

            if (count($block) == 0) {
                Arr::forget($this->content, $key);
            } else {
                $this->placeBlock($block, $key);
            }
        }
    }

    private function isNotText(string $key): bool
    {
        return !str_contains($key, 'textblock');
    }

    private function placeBlock(array $block, string $key): void
    {
        $this->content[$key] = count($block) == 1
            ? array_values($block)
            : $this->insertEmptyLines($block);
    }

    private function insertEmptyLines(array $block): array
    {
        $newBlock = [];

        foreach ($block as $line) {
            $newBlock[] = $line;

            if ($this->isParagraph($line)) {
                $newBlock[] = '';
            }
        }

        return trim(array_reverse($newBlock)[0])
            ? $newBlock
            : Arr::delete($newBlock);
    }

    private function isParagraph(string $line): bool
    {
        return preg_match('/[a-zA-Z]/', substr($line, 0, 1));
    }
}
