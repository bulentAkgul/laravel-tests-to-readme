<?php

namespace Bakgul\LaravelTestsToReadme\Services;

use Bakgul\LaravelHelpers\Helpers\Arr;
use Bakgul\LaravelHelpers\Helpers\Str;
use Bakgul\LaravelTestsToReadme\Tasks\HandleMultiline;
use Bakgul\LaravelTestsToReadme\Tasks\PrintVariable;

class CodeBlockService
{
    public function __construct(private array $props)
    {
    }

    public function __invoke(): array
    {
        $this->execute();

        return array_map(
            fn ($x) => $this->clearLine($x),
            array_map(fn ($x) => "// {$x}", $this->makeParts())
        );
    }

    private function clearLine(string $line): string
    {
        return match (true) {
            $line == "// returns: 'null'" => "// null",
            $line == "// returns: 'true'" => "// true",
            $line == "// returns: 'false'" => "// false",
            $line != '// ' => str_replace(['// code ', 'returns: '], '', $line),
            default => ''
        };
    }

    private function makeParts(): array
    {
        return array_values(array_filter([
            $x = $this->wordWrap($this->sentence($this->props['test'])),
            $x ? '' : null,
            $x = $this->wordWrap($this->props['message'] ?? null),
            $x ? '' : null,
            "code {$this->makeCode()}",
            '',
            "returns: {$this->props['result']}"
        ], fn ($x) => !is_null($x)));
    }

    private function sentence(string $str): string
    {
        $parts = explode('_', Str::snake($str));
        $offset = $parts[0] == 'test' ? 1 : 0;

        return ucfirst(implode(' ', array_slice($parts, $offset))) . '.';
    }

    private function wordWrap(?string $str): ?string
    {
        if (is_null($str)) return $str;

        return wordwrap($str, config('to-readme.line_length') - 4, PHP_EOL . '// ');
    }

    private function makeCode(): string
    {
        $definition = $this->makeCodeDefinition();

        return str_replace(',', ', ', str_replace(', ', ',', implode('', [
            $definition,
            $this->arguments($definition),
            ');'
        ])));
    }

    private function makeCodeDefinition(): string
    {
        $isStatic = $this->isStatic();

        return implode('', [
            $isStatic ? '' : '(new ',
            $this->props['class']->getShortName(),
            $this->construct(),
            $isStatic ? '' : ')',
            $isStatic ? '::' : '->',
            $this->props['method']->getName(),
            '(',
        ]);
    }

    private function isStatic(): bool
    {
        return $this->props['method']->isStatic();
    }

    private function construct(): string
    {
        return $this->props['class']->getConstructor() ? '()' : '';
    }

    private function arguments(string $definition): string
    {
        $args = array_map(
            fn ($x) => $this->print($x),
            $this->props['args']
        );

        return $this->multiline($args, $definition) ?: implode(', ', $args);
    }

    private function multiline(array $args, string $definition): string
    {
        return Str::enclose($this->setArguments($args, $definition), PHP_EOL);
    }

    private function setArguments(array $args, string $definition): string
    {
        return trim((new HandleMultiline([$definition, $args]))(), ',');
    }

    private function print(mixed $input): string
    {
        return PrintVariable::_($input);
    }

    private function execute(): void
    {
        if (Arr::has($this->props, 'result')) return;

        try {
            $this->props['result'] = $this->print($this->props['method']
                ->invokeArgs(
                    $this->props['class'],
                    $this->props['args']
                ));
        } catch (\Throwable $th) {
            $this->props['result'] = 'No return value could have been produced.';
        }
    }
}
