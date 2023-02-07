<?php

namespace Bakgul\LaravelTestsToReadme\Services;

use Bakgul\LaravelHelpers\Helpers\Arr;
use Bakgul\LaravelTestsToReadme\Builders\MethodBuilder;
use Bakgul\LaravelTestsToReadme\Builders\PhpdocBuilder;
use Bakgul\LaravelTestsToReadme\Builders\WarningBuilder;
use Bakgul\LaravelTestsToReadme\Resolvers\ParameterResolver;
use Bakgul\LaravelTestsToReadme\Resolvers\PhpdocResolver;
use Bakgul\LaravelTestsToReadme\Resolvers\ReturnTypeResolver;
use ReflectionMethod;

class MethodBlockService
{
    private array|string $phpdoc = [];
    private string $originalDoc;
    private array $parameters;
    private array $returns;

    public function __construct(private ReflectionMethod $method)
    {
        $this->originalDoc = $method->getDocComment();
        $this->phpdoc = $this->resolvePhpdoc();
        $this->parameters = $this->resolveParameters();
        $this->returns = $this->resolveReturns();
    }

    public function __invoke(): array
    {
        return Arr::resolve([
            $this->warnings(),
            $this->phpdoc(),
            $this->method()
        ]);
    }

    private function resolvePhpdoc(): array|string
    {
        return (new PhpdocResolver($this->originalDoc))();
    }

    private function resolveParameters(): array
    {
        return (new ParameterResolver(
            $this->method->getParameters(),
            $this->phpdoc['param'] ?? []
        ))();
    }

    private function resolveReturns(): array
    {
        return (new ReturnTypeResolver(
            $this->method->getReturnType(),
            $this->phpdoc['return'] ?? []
        ))();
    }

    private function warnings(): string
    {
        return (new WarningBuilder)($this->parameters);
    }

    private function phpdoc(): string
    {
        return (new PhpdocBuilder($this->phpdoc))() ?? $this->originalDoc;
    }

    private function method(): string
    {
        return (new MethodBuilder($this->method, $this->parameters, $this->returns))();
    }
}
