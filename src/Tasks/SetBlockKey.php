<?php

namespace Bakgul\LaravelTestsToReadme\Tasks;

use Bakgul\LaravelHelpers\Helpers\Str;
use Bakgul\LaravelTestsToReadme\Utilities\GetIdentifiers;

class SetBlockKey
{
    public function __construct(
        private string $line,
        private string $currentBlock = '',
        private array $identifiers = [],
        private string $prefix = '',
    ) {
        $this->identifiers = $identifiers ?: GetIdentifiers::_();
        $this->prefix = $prefix ?: config('to-readme.key_prefix', '');
    }

    public function __invoke(): string
    {
        return trim(str_replace('#', '', implode('-', array_filter([
            $key = $this->prefix($this->keyBase()),
            ...$this->uniqueIndex($key)
        ]))));
    }

    private function prefix(string $base): string
    {
        return $this->isBaseModifyable($base)
            ? Str::prepend($base, $this->prefix ?? '', '-')
            : $base;
    }

    private function keyBase(): string
    {
        return match (true) {
            $this->hasClassIndetifier() => $this->setKey('class'),
            $this->hasMethodIndetifier() => $this->setKey('method'),
            $this->hasCodeIdentifier() => 'codeblock',
            default => $this->currentBlock ? '' : 'textblock'
        };
    }

    private function hasClassIndetifier(): bool
    {
        return $this->hasIdentifier('class');
    }

    private function hasMethodIndetifier(): bool
    {
        return $this->hasIdentifier('method');
    }

    private function hasCodeIdentifier(): bool
    {
        return $this->hasIdentifier('code');
    }

    private function hasIdentifier(string $key): bool
    {
        return substr($this->line, 0, strlen($this->identifiers[$key]))
            == $this->identifiers[$key];
    }

    private function setKey(string $type): string
    {
        $this->setKeyPrefix($type);

        return trim($this->line);
    }

    private function setKeyPrefix(string $type): void
    {
        $this->prefix = $type == 'class' ? 'class' : $this->getMethodName();

        config()->set('to-readme.key_prefix', $this->prefix);
    }

    private function getMethodName(): string
    {
        return trim(str_replace($this->identifiers['method'], '', $this->line));
    }

    private function uniqueIndex(string $base): array
    {
        return $this->isBaseModifyable($base)
            ? array_map(fn () => rand(100, 999), range(1, 3))
            : [];
    }

    private function isBaseModifyable(string $base): bool
    {
        return Str::hasSome($base, ['textblock', 'codeblock']);
    }
}
