<?php

declare(strict_types=1);

namespace Accelade\Actions\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class MakeActionCommand extends GeneratorCommand
{
    protected $signature = 'make:action {name : The name of the action class}
                            {--force : Create the class even if the action already exists}';

    protected $description = 'Create a new action class';

    protected $type = 'Action';

    protected function getStub(): string
    {
        return __DIR__.'/../../stubs/action.stub';
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\Actions';
    }

    protected function buildClass($name): string
    {
        $stub = parent::buildClass($name);

        return str_replace(
            '{{ actionName }}',
            Str::of(class_basename($name))->replace('Action', '')->kebab()->toString(),
            $stub
        );
    }
}
