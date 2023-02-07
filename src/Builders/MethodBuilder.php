<?php

namespace Bakgul\LaravelTestsToReadme\Builders;

use Bakgul\LaravelHelpers\Helpers\Arr;
use Bakgul\LaravelTestsToReadme\Tasks\HandleMultiline;
use Bakgul\LaravelTestsToReadme\Tasks\PrintVariable;
use Reflection;
use ReflectionMethod;

class MethodBuilder
{
    public function __construct(
        private ReflectionMethod $method,
        private array $parameters,
        private array $returns
    ) {
    }

    public function __invoke()
    {
        $parts = [
            $this->getModifiers(),
            'function',
            $this->method->getName(),
            $this->stringifyParameters(),
            $this->stringifyReturns(),
        ];

        return $this->multiline($parts) ?: $this->singleLine($parts);
    }



    private function getModifiers(): string
    {
        return implode(' ', Reflection::getModifierNames($this->method->getModifiers()));
    }

    private function stringifyParameters(): array
    {
        $params = [];

        foreach ($this->parameters as $param) {
            $params[] = $this->stringifyParameter($param);
        }

        return $params;
    }

    private function stringifyParameter(array $param): string
    {
        return implode(' ', array_filter([
            $this->setParamTypes($param['types']),
            $this->setParamName($param),
            $this->setParamDefault($param),
        ]));
    }

    private function setParamTypes(array $types): string
    {
        $nullable = Arr::in(Arr::pluck($types, 'nullable'), true);
        $names = Arr::pluck($types, 'name');

        return $this->stringifyWithNull($names, $nullable);
    }

    private function stringifyWithNull(array $types, ?bool $nullable = null): string
    {
        $nullable = is_null($nullable) ? Arr::in($types, 'null') : $nullable;

        $types = Arr::resolve($types, fn ($x) => $x != 'null');

        if ($this->isWithoutNull($types, $nullable)) {
            return implode('|', $types);
        }

        if (count($types) > 1) {
            $types[] = 'null';
        } else {
            $types[0] = "?{$types[0]}";
        }

        return implode('|', $types);
    }

    private function isWithoutNull(array $types, ?bool $nullable): bool
    {
        return !$nullable || count($types) == 0 || in_array('mixed', $types);
    }

    private function setParamName(array $param): string
    {
        return implode('', array_filter([
            $param['isVariadic'] ? '...' : '',
            $param['isReference'] ? '&' : '',
            '$' . $param['name'],
        ]));
    }

    private function setParamDefault(array $param): string
    {
        return $param['hasDefault'] ? $this->printParamDefault($param) : '';
    }

    private function printParamDefault(array $param): string
    {
        return '= ' . PrintVariable::_($param['default']);
    }

    private function stringifyReturns(): string
    {
        return $this->stringifyWithNull($this->returns['types']);
    }

    private function multiline(array $parts): string
    {
        $args = (new HandleMultiline($parts))();

        return $args ? $this->makeLine($parts, $args, PHP_EOL) : '';
    }

    private function singleLine(array $parts): string
    {
        return $this->makeLine($parts, implode(', ', $parts[3]), '');
    }

    private function makeLine(array $parts, array|string $params, $glue): string
    {
        return implode($glue, [
            implode(' ', array_slice($parts, 0, 3)) . '(',
            ...(array) $params,
            ')' . ($parts[4] ? ": {$parts[4]}" : '')
        ]);
    }
}
