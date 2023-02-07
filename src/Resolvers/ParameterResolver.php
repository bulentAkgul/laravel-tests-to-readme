<?php

namespace Bakgul\LaravelTestsToReadme\Resolvers;

use Bakgul\LaravelHelpers\Helpers\Arr;
use Bakgul\LaravelTestsToReadme\Adapters\PhpdocParamToAssoc;
use Bakgul\LaravelTestsToReadme\Adapters\ReflectionParamToAssoc;
use Bakgul\LaravelTestsToReadme\Helpers\Merge;
use Bakgul\LaravelTestsToReadme\Tasks\WarnForInconsistentInputs;

class ParameterResolver
{
    private array $methodParams;
    private array $phpdocParams;

    public function __construct(array $parameters, array $phpdoc)
    {
        $this->methodParams = ReflectionParamToAssoc::adapt($parameters);
        $this->phpdocParams = PhpdocParamToAssoc::adapt($phpdoc ?? []);
    }

    public function __invoke(): array
    {
        $params = [];

        foreach ($this->methodParams as $i => $param) {
            $params[] = [
                ...$param,
                'types' => $this->mergeTypes($param, $i),
                'warning' => $this->warning($i),
            ];
        }

        return $this->orderParamTypes($params);
    }

    private function mergeTypes(array $param, int $i): array
    {
        return $this->isNotMergable($param)
            ? $param['types']
            : Arr::get($this->phpdocParams, "{$i}.types", []);
    }

    private function isNotMergable(array $param): bool
    {
        return $param['types'] || Merge::false();
    }

    private function warning(int $i): string
    {
        return (new WarnForInconsistentInputs($this->methodParams, $this->phpdocParams, $i))();
    }

    private function orderParamTypes(array $params): array
    {
        if (empty($this->phpdocParams)) return $params;

        foreach ($params as &$param) {
            if (count($param['types']) < 2) continue;

            if ($param['warning']) continue;

            $param['types'] = array_map(
                fn ($x) => Arr::first($param['types'], fn ($y) => $y['name'] == $x['name']),
                Arr::value($this->phpdocParams, $param['position'], 'position', pluck: 'types') ?? []
            );
        }

        return $params;
    }
}
