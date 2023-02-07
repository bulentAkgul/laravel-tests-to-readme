<?php

namespace Bakgul\LaravelTestsToReadme\Tasks;

use Bakgul\LaravelHelpers\Helpers\Arr;

class HandleMultiline
{
    private int $argKey;

    public function __construct(private array $parts, private bool $isArray = false)
    {
        $this->argKey = $this->argKey();
    }

    private function argKey(): int
    {
        return array_key_first(Arr::where($this->parts, fn ($x) => is_array($x)));
    }

    public function __invoke(): string
    {
        return $this->isMultiline() ? $this->multiline() : '';
    }

    private function isMultiline(): bool
    {
        $isLong = $this->isLineTooLong();

        return $isLong || $this->hasTooManyParams($isLong);
    }

    private function isLineTooLong(): bool
    {
        return $this->lineLength() > $this->lineLimit();
    }

    private function lineLength(): int
    {
        return array_reduce($this->parts, fn ($p, $c) => $p + strlen(
            is_array($c) ? implode(', ', $c) : $c
        ), 6);
    }

    private function lineLimit(): int
    {
        return config('to-readme.line_length');
    }

    private function hasTooManyParams(bool $isLong): bool
    {
        $params = $this->paramCount();

        return $params > $this->forcedLimit() || $isLong && $params > $this->paramLimit();
    }

    private function paramCount(): int
    {
        return count($this->parts[$this->argKey]);
    }

    private function forcedLimit(): int
    {
        return config('to-readme.force_break_for_multiline_parameters');
    }

    private function paramLimit(): int
    {
        return config('to-readme.method_declaration_single_line_parameters');
    }

    private function multiline(): array|string
    {
        return $this->isArray ? $this->args() : implode(PHP_EOL, $this->args());
    }

    private function args(): array
    {
        return array_map(fn ($x) => "    {$x},", $this->parts[$this->argKey]);
    }
}
