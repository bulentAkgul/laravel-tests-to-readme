<?php

namespace Bakgul\LaravelTestsToReadme\Resolvers;

use Bakgul\LaravelHelpers\Helpers\Arr;
use Bakgul\LaravelHelpers\Helpers\Str;
use Bakgul\LaravelTestsToReadme\Helpers\Merge;
use Bakgul\LaravelTestsToReadme\Tasks\WarnForInconsistentInputs;
use Bakgul\LaravelTestsToReadme\Utilities\SetTypes;
use ReflectionNamedType;
use ReflectionUnionType;

class ReturnTypeResolver
{
    private array $methodReturnTypes;

    public function __construct(
        ReflectionNamedType|ReflectionUnionType|null $returns,
        private array $phpdocReturnTypes
    ) {
        $this->methodReturnTypes = SetTypes::_($returns);
    }

    public function __invoke(): array
    {
        return $this->build($this->setFromTypeHint(), $this->setFromPhpdoc());
    }

    private function build(array $typehint, array $phpdoc): array
    {
        return [
            'types' => $typehint ?: (Merge::true() ? $phpdoc : []),
            'warning' => $this->warning($typehint, $phpdoc)
        ];
    }

    private function setFromTypeHint(): array
    {
        $returns = [];

        foreach ($this->methodReturnTypes as $return) {
            $returns[] = $this->setName($return);
            $returns[] = $this->setNull($return);
        }

        return Arr::unique($returns);
    }

    private function setFromPhpdoc(): array
    {
        return is_int(Arr::containsAt($this->phpdocReturnTypes, '?'))
            ? [...array_map(fn ($x) => str_replace('?', '', $x), $this->phpdocReturnTypes), 'null']
            : $this->phpdocReturnTypes;
    }


    private function setName(ReflectionNamedType $return): string
    {
        return $this->useName($return)
            ? $return->getName()
            : Str::getTail($return->getName(), '\\');
    }

    private function useName(ReflectionNamedType $return): bool
    {
        return $return->isBuiltin()
            || config('to-readme.show_full_class_name_when_type_hint');
    }

    private function setNull(ReflectionNamedType $return): string
    {
        return $return->allowsNull() ? 'null' : '';
    }

    private function warning(array $typehint, array $phpdoc): string
    {
        return (new WarnForInconsistentInputs($typehint, $phpdoc, null))();
    }
}
