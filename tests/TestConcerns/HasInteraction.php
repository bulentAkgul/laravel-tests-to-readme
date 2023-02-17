<?php

namespace Bakgul\LaravelTestsToReadme\Tests\TestConcerns;

use Bakgul\LaravelTestsToReadme\Tests\TestTasks\InteractWithFile;

trait HasInteraction
{
    protected InteractWithFile $file;

    protected function file(array|string $scenario = '', string $job = 'in', string $class = ''): void
    {
        $this->file = (new InteractWithFile([
            'class' => $class ?: 'TestClass' . rand(0, 10000),
            'content' => $this->input($scenario, $job),
        ]));
    }

    protected function deleteClass(): void
    {
        $this->file->delete();
    }
}
