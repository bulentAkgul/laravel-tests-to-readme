<?php

namespace Bakgul\LaravelTestsToReadme\Tasks;

use Bakgul\LaravelHelpers\Helpers\Arr;

class WarnForInconsistentInputs
{
    private array $phpdoc;
    private int $phpdocCount;
    private array $typehint;
    private int $typehintCount;
    private bool $isParameter;

    public function __construct(array $typehint, array $phpdoc, ?int $index = 0)
    {
        $this->isParameter = is_int($index);

        [$this->phpdoc, $this->phpdocCount] = $this->set($phpdoc, $index);
        [$this->typehint, $this->typehintCount] = $this->set($typehint, $index);
    }

    private function set(array $inputs, ?int $index): array
    {
        $inputs = $this->prepareInputs($inputs);

        return [
            is_null($index) ? $inputs : ($inputs[$index] ?? []),
            count($inputs)
        ];
    }

    private function prepareInputs(array $inputs): array
    {
        if ($this->isParameter) return $inputs;

        $returns = [];

        foreach ($inputs as $type) {
            $returns['types'][]['name'] = $type;
        }

        return $returns;
    }

    public function __invoke(): string
    {
        return $this->warning($this->evaluate());
    }

    private function evaluate(): string
    {
        return $this->isParameter
            ? ($this->mainWarning() ?: $this->typeWarning())
            : $this->returnWarning();
    }

    private function mainWarning(): string
    {
        return match (true) {
            $this->noPhpdoc() => '',
            $this->isParamsCountDifferent() => 'mismatched_parameter_count',
            $this->isParamMisplaced() => 'misplaced_parameter',
            $this->isVariadicDifferent() => 'mismatched_variadic',
            $this->isReferenceDifferent() => 'mismatched_reference',
            default => ''
        };
    }

    private function typeWarning(): string
    {
        return match (true) {
            $this->noType() => '',
            $this->isTypesCountDifferent() => 'mismatched_type_count',
            $this->isTypeDifferent() => 'mismatched_types',
            $this->isNullabalityDifferent() => 'missmatched_nullable_types',
            default => ''
        };
    }

    private function returnWarning(): string
    {
        return $this->typeWarning();
    }

    private function noPhpdoc(): bool
    {
        return empty($this->phpdoc);
    }

    private function noType(): bool
    {
        return empty($this->typehint['types']) || empty($this->phpdoc['types']);
    }

    private function isParamsCountDifferent(): bool
    {
        return $this->typehintCount != $this->phpdocCount;
    }

    private function isParamMisplaced(): bool
    {
        return $this->typehint['name'] != Arr::get($this->phpdoc, 'name');
    }

    private function isVariadicDifferent(): bool
    {
        return $this->typehint['isVariadic'] != $this->phpdoc['isVariadic'];
    }

    private function isReferenceDifferent(): bool
    {
        return $this->typehint['isReference'] != $this->phpdoc['isReference'];
    }

    private function isTypesCountDifferent(): bool
    {
        return count($this->dropNull($this->typehint['types']))
            != count($this->dropNull($this->phpdoc['types']));
    }

    private function isTypeDifferent(): bool
    {
        return $this->getTypes('typehint') != $this->getTypes('phpdoc');
    }

    private function getTypes(string $src): array
    {
        return array_map(
            fn ($x) => $x == 'boolean' ? 'bool' : $x,
            array_values(Arr::sort(
                Arr::pluck($this->dropNull($this->$src['types']), 'name')
            ))
        );
    }

    private function isNullabalityDifferent(): bool
    {
        return $this->isParameter ? $this->checkNullable() : $this->checkNull();
    }

    private function checkNullable(): bool
    {
        return $this->getNullable('typehint')
            != $this->getNullable('phpdoc');
    }

    private function getNullable(string $src): array
    {
        return Arr::pluck(array_map(
            fn ($x) => [...$x, 'nullable' => $x['name'] == 'mixed' ? true : $x['nullable']],
            $this->$src['types']
        ), 'nullable');
    }

    private function checkNull(): bool
    {
        return Arr::where($this->typehint['types'], fn ($x) => $x['name'] == 'null')
            != Arr::where($this->phpdoc['types'], fn ($x) => $x['name'] == 'null');
    }

    private function dropNull(array $types): array
    {
        return Arr::resolve($types, $this->isParameter ? null : fn ($x) => $x['name'] != 'null');
    }

    private function warning(string $key): string
    {
        return $key ? config("to-readme.warnings.{$key}") : '';
    }
}
