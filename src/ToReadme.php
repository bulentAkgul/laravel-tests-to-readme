<?php

namespace Bakgul\LaravelTestsToReadme;

use Bakgul\LaravelHelpers\Helpers\Arr;
use Bakgul\LaravelTestsToReadme\Services\CodeBlockService;
use Bakgul\LaravelTestsToReadme\Services\MethodBlockService;
use Bakgul\LaravelTestsToReadme\Services\ReadmeContentService;
use Bakgul\LaravelTestsToReadme\Tasks\SetBlockKey;
use Bakgul\LaravelTestsToReadme\Utilities\SetTitle;
use ReflectionClass;
use ReflectionMethod;

class ToReadme
{
    private array $implementation;
    private array $content;
    private ReflectionClass $reflection;
    private ReadmeContentService $readme;

    public function __construct(array $implementation)
    {
        $this->setProps($implementation);
    }

    public function setProps(array $implementation): void
    {
        $this->implementation = $implementation;
        $this->reflection = new ReflectionClass($implementation['class']);
        $this->readme = new ReadmeContentService($this->reflection->getFileName());
    }

    public function write(): void
    {
        $this->setContent()
            ->makeMethodTitle()
            ->makeMethodBlock()
            ->makeCodeBlock()
            ->writeToFile();
    }

    private function setContent(): self
    {
        $this->content = $this->read() ?: $this->init();

        return $this;
    }

    private function read(): array
    {
        return $this->readme->read();
    }

    private function init(): array
    {
        return $this->readme->init($this->implementation);
    }

    private function makeMethodTitle(): self
    {
        if ($this->isMethodNew()) $this->setMethodTitle();

        return $this;
    }

    private function isMethodNew(): bool
    {
        return Arr::hasNot($this->content, $this->implementation['method']);
    }

    private function setMethodTitle(): void
    {
        $this->content[$this->implementation['method']] = [
            SetTitle::_('method', $this->implementation['method'])
        ];
    }

    private function makeMethodBlock(): self
    {
        $this->intoContent($this->intoBlock(
            (new MethodBlockService($this->getMethod()))()
        ), $this->setMethodKey());

        return $this;
    }

    private function setMethodKey(): string
    {
        return $this->findMethodBlockKey() ?: $this->blockKey();
    }

    private function findMethodBlockKey(): ?string
    {
        return array_key_first(Arr::where($this->content, $this->find()));
    }

    private function find(): callable
    {
        return fn ($block) => str_contains(
            implode('', $block),
            "function {$this->implementation['method']}("
        );
    }

    private function getMethod(): ReflectionMethod
    {
        return $this->reflection->getMethod($this->implementation['method']);
    }

    private function makeCodeBlock(): self
    {
        $this->intoContent(
            $this->intoBlock($this->generateCodeBlock()),
            $this->blockKey()
        );

        return $this;
    }

    private function generateCodeBlock(): array
    {
        return (new CodeBlockService([
            ...$this->implementation,
            'class' => $this->reflection,
            'method' => $this->getMethod(),
        ]))();
    }

    private function intoBlock(array $content): array
    {
        return [
            config('to-readme.code_block_identifier'),
            ...$content,
            '```'
        ];
    }

    private function intoContent(array $block, string $key): void
    {
        $this->content[$key] = $block;
    }

    private function blockKey(): string
    {
        return (new SetBlockKey(
            config("to-readme.code_block_identifier"),
            prefix: $this->implementation['method']
        ))();
    }

    private function writeToFile(): void
    {
        $this->readme->write($this->content);
    }
}
