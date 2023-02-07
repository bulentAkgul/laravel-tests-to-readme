<?php

namespace Bakgul\LaravelTestsToReadme\Services;

use Bakgul\LaravelHelpers\Helpers\Path;
use Bakgul\LaravelHelpers\Helpers\Str;
use Bakgul\LaravelTestsToReadme\Tasks\GetClosestReadme;
use Bakgul\LaravelTestsToReadme\Tasks\ReadFileContent;
use Bakgul\LaravelTestsToReadme\Tasks\WriteFileContent;
use Bakgul\LaravelTestsToReadme\Utilities\SetTitle;

class ReadmeContentService
{
    public function __construct(private string $path)
    {
        $this->path = $this->readmePath();
    }

    private function readmePath(): string
    {
        return Path::glue([$this->filePath(), $this->fileName()]);
    }

    private function filePath(): string
    {
        return GetClosestReadme::handle(Str::dropTail($this->path));
    }

    private function fileName(): string
    {
        return str_replace('.php', '.md', Str::getTail($this->path));
    }

    public function init(array $implementation): array
    {
        return [$implementation['class'] => [
            SetTitle::_('class', Str::getTail($implementation['class'], '\\'))
        ]];
    }

    public function read(): array
    {
        return (new ReadFileContent($this->path))();
    }

    public function write(array $content): void
    {
        (new WriteFileContent($this->path, $content))();
    }
}
