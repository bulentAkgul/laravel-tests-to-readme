<?php

namespace Bakgul\LaravelTestsToReadme\Resolvers;

use Bakgul\LaravelHelpers\Helpers\Arr;
use Bakgul\LaravelHelpers\Helpers\Str;

class PhpdocResolver
{
    private array $output;
    private array $phpdoc;

    public function __construct(string $phpdoc)
    {
        $this->phpdoc = array_filter(explode(PHP_EOL, $phpdoc));
    }

    public function __invoke(): array
    {
        [$message, $tags] = $this->divideDoc();

        $this->setMessage($message);

        $this->setTags($tags);

        $this->retouchParams();

        $this->flattenTags();

        $this->simplifyTags();

        return $this->output;
    }

    private function divideDoc(): array
    {
        $lines = $this->removeLines();

        $doc = [];

        foreach ($lines as $line) {
            $doc[$this->docType($line, $doc)][] = $line;
        }

        return [$doc['message'] ?? [], $doc['tags'] ?? []];
    }

    private function docType(string $line, array $doc): string
    {
        return $this->isMessage($line, $doc) ? 'message' : 'tags';
    }

    private function isMessage(string $line, array $doc): bool
    {
        return Str::hasNot($line, '* @')
            && $this->hasMessageOnly($doc);
    }

    private function hasMessageOnly(array $doc): bool
    {
        return empty(array_filter(array_keys($doc), fn ($x) => $x != 'message'));
    }

    private function removeLines(): array
    {
        return array_filter($this->phpdoc, $this->uselessLines());
    }

    private function uselessLines(): callable
    {
        return fn ($x) => Arr::out(['/**', '*/', '*'], trim($x));
    }

    private function setMessage(array $message): void
    {
        $this->output['message'] = array_map(
            fn ($x) => trim(str_replace(' * ', '', $x)),
            $message
        );
    }

    private function setTags(array $tags): void
    {
        foreach ($tags as $tag) {
            $parts = $this->breakDown($tag);

            str_contains($tag, '* @')
                ? $this->addToList($parts)
                : $this->appendToLast($parts);
        }
    }

    private function breakDown(string $tag): array
    {
        $parts = array_filter(explode(' ', str_replace(['* @', '*'], '', trim($tag))));

        $output = [array_shift($parts)];

        $parameter = $this->getParameter($parts);

        if (!$parameter) return [...$output, ...$parts];

        $output[] = $this->addTypes($parts, $parameter);

        $output[] = $parameter['name'];

        $output[] = $this->addExplanation($parts, $parameter);

        return array_filter($output);
    }

    private function getParameter(array $parts): array
    {
        $parameter = Arr::where($parts, fn ($x) => substr($x, 0, 1) == '$');

        return $parameter ? [
            'index' =>  $i = array_key_first($parameter),
            'name' => $parameter[$i]
        ] : [];
    }

    private function addTypes(array $parts, array $parameter): string
    {
        return $this->addPart($parts, 0, $parameter['index']);
    }

    private function addExplanation(array $parts, array $parameter): string
    {
        return $this->addPart($parts, $parameter['index'] + 1);
    }

    private function addPart(array $parts, int $offset, ?int $length = null): string
    {
        return implode(' ', array_slice($parts, $offset, $length));
    }

    private function addToList(array $parts): void
    {
        $this->output[$parts[0]][] = Arr::resolve(array_slice($parts, 1));
    }

    private function appendToLast(array $parts): void
    {
        $key = array_key_last($this->output);
        $i = count($this->output[$key]) - 1;

        $this->output[$key][$i] = [...$this->output[$key][$i], ...$parts];
    }

    private function retouchParams(): void
    {
        foreach ($this->output['param'] ?? [] as $i => $param) {
            $this->output['param'][$i] = $this->mergeParamParts($param);
        }
    }

    private function mergeParamParts(array $param): array
    {
        return array_filter([$param[0], $param[1], implode(' ', array_slice($param, 2))]);
    }

    private function flattenTags(): void
    {
        foreach (['return', 'api', 'license'] as $tag) {
            if (Arr::hasNot($this->output, $tag)) continue;

            $this->output[$tag] = explode('|', Arr::flatten($this->output[$tag])[0] ?? '');
        }
    }

    private function simplifyTags(): void
    {
        foreach (['api', 'license'] as $tag) {
            if (Arr::hasNot($this->output, $tag)) continue;

            $this->output[$tag] = implode(' ', $this->output[$tag]);
        }
    }
}
