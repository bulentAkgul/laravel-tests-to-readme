<?php

namespace Bakgul\LaravelTestsToReadme\Tests\TestTasks;

use ReflectionClass;
use ReflectionMethod;

class Reflect
{
    public static function class(string $class = 'TestClass'): ReflectionClass
    {
        return new ReflectionClass("Bakgul\LaravelTestsToReadme\Tests\TestFiles\\{$class}");
    }

    public static function method(string $class = 'TestClass', string $method = 'dummyMethod'): ReflectionMethod
    {
        return self::class($class)->getMethod($method);
    }

    public static function parameters(string $class = 'TestClass', string $method = 'dummyMethod'): array
    {
        return self::method($class, $method)->getParameters();
    }

    public static function types(string $class = 'TestClass', string $method = 'dummyMethod'): array
    {
        return array_map(fn ($x) => $x->getType(), self::parameters($class, $method));
    }

    public static function return(string $class = 'TestClass', string $method = 'dummyMethod')
    {
        return self::method($class, $method)->getReturnType();
    }

    public static function phpdoc(string $class = 'TestClass', string $method = 'dummyMethod'): string
    {
        return self::method($class, $method)->getDocComment();
    }
}
