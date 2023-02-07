<?php

namespace Bakgul\LaravelTestsToReadme\Tests\TestTasks;

use Bakgul\LaravelHelpers\Helpers\Folder;
use Bakgul\LaravelHelpers\Helpers\Path;
use Bakgul\LaravelHelpers\Helpers\Str;
use Illuminate\Support\Facades\File;

class InteractWithFile
{
    private string $fileName;
    private string $filePath;
    private string $fullPath;
    private string $namespace;
    private string $readmePath;
    private string $readmeFile;

    public function __construct(private array $props)
    {
        $this->fileName = $props['class'] ?? 'TestClass';
        $this->filePath = "/../TestFiles/{$this->fileName}.php";
        $this->fullPath = __DIR__ . $this->filePath;
        $this->namespace = $props['namespace'] ?? 'Bakgul\LaravelTestsToReadme\Tests\TestFiles';

        $this->create(...Str::separateTail($this->fullPath));
    }

    public function create(string $path, string $file): void
    {
        if ($this->isCreatable()) Folder::add($path, $file, $this->content());
    }

    private function isCreatable(): bool
    {
        return !file_exists($this->fullPath)
            || ($this->props['force'] ?? false);
    }

    public function namespace(): string
    {
        return $this->namespace;
    }

    public function fileName(): string
    {
        return $this->fileName;
    }

    public function class(): string
    {
        return Path::glue([$this->namespace(), $this->fileName()], '\\');
    }

    public function className(): string
    {
        return $this->fileName();
    }

    public function fullPath(): string
    {
        return $this->fullPath;
    }

    public function content(): string
    {
        return str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ content }}'],
            [$this->namespace, $this->fileName, $this->props['content'] ?? $this->default],
            file_get_contents(__DIR__ . '/../TestFiles/class.stub')
        );
    }

    public function read(string $src = 'fullPath'): string
    {
        return file_get_contents($this->$src);
    }

    public function readme(): string
    {
        return file_get_contents($this->setReadmeFile());
    }

    public function makeReadmeDir(): void
    {
        Path::complete($this->setReadmeFolder());
    }

    public function copyReadme(): void
    {
        $file = 'TestClass.md';

        $this->makeReadmeDir();

        File::copy(
            Str::changeTail($this->fullPath, $file),
            $this->readmeFile ?? "{$this->readmePath}/{$file}"
        );
    }

    public function deleteReadme(): void
    {
        File::deleteDirectory($this->readmePath);
    }

    private function setReadmeFolder(): string
    {
        return $this->readmePath = Str::changeTail($this->fullPath, 'readme');
    }

    private function setReadmeFile(): string
    {
        return $this->readmeFile = Str::changeTail($this->fullPath, "readme/{$this->fileName}.md");
    }

    public function readmePath(): string
    {
        return $this->readmePath;
    }

    public function write(): void
    {
    }

    public function add(): void
    {
    }

    public function remove(): void
    {
    }

    public function change(): void
    {
    }

    public function delete(): void
    {
        unlink($this->fullPath);
    }

    private $default = <<<EOD
        /**
         * Does yyy stuff.
         * 
         * @param array|string &\$x4
         * @param string|null \$x1
         * @param string \$x2
         * @param mixed ...\$x3
         * @return string
         */
        final public static function yyy(array|string &\$x4, ?string \$x1, string \$x2 = "dsd", mixed ...\$x3): ?string
        {
            return \$x1 . \$x2;
        }
    EOD;
}
