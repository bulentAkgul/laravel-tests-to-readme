<?php

namespace Bakgul\LaravelTestsToReadme\Builders;

use Bakgul\LaravelHelpers\Helpers\Arr;
use Bakgul\LaravelTestsToReadme\Helpers\Merge;

class PhpdocBuilder
{
    public function __construct(private array $phpdoc)
    {
    }

    public function __invoke(): ?string
    {
        return Merge::true() ? $this->output($this->phpdoc()) : null;
    }

    private function output(array $phpdoc): string
    {
        return str_replace(" * \n */", ' */', implode("\n", $phpdoc));
    }

    private function phpdoc(): array
    {
        return [
            '/**',
            ...$this->message(),
            ...$this->tags(),
            ' */',
        ];
    }

    private function message(): array
    {
        return array_map(fn ($x) => " * {$x}", [...$this->phpdoc['message'], '']);
    }

    private function tags(): array
    {
        $tags = [];

        foreach ($this->getTags() as $tag => $values) {
            $tags = [...$tags, ...$this->setTags($tag, $values), ' * '];
        }

        return Arr::delete($tags, -1);
    }

    private function getTags(): array
    {
        return Arr::except($this->phpdoc, ['message', 'return']);
    }

    private function setTags(string $tag, array|string $values): array
    {
        if ($tag == 'param') return $this->setParam($values);

        $tags = [];

        foreach ((array) $values as $value) {
            $tags[] = implode(' ', array_filter([" * @{$tag}", ...(array) $value]));
        }

        return $tags;
    }

    private function setParam($values): array
    {
        $params = [];

        foreach ($values as $value) {
            if (count($value) < 3) continue;

            $params[] = implode(' ', [" * @param", ...array_slice($value, 1, 2)]);
        }

        return $params;
    }
}
