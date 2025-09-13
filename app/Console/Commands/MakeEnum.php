<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeEnum extends Command
{
    protected $signature = 'make:enum {name : The name of the enum}
                            {--type= : The type of the enum}';

    protected $description = 'Create a new enum';

    public function handle()
    {
        $name = $this->argument('name');
        $type = $this->option('type');

        $stub = $this->getStub();
        $content = $this->replaceStubContent($stub, $name, $type);

        $path = $this->getPath($name);

        if (File::exists($path)) {
            $this->error("Enum {$name} already exists!");
            return Command::FAILURE;
        }

        File::put($path, $content);

        $this->info("Enum {$name} created successfully.");

        return Command::SUCCESS;
    }

    protected function getStub()
    {
        $stubPath = base_path('stubs/enum.stub');

        if (!File::exists($stubPath)) {
            $stubPath = __DIR__ . '/stubs/enum.stub';
        }

        return File::get($stubPath);
    }

    protected function replaceStubContent($stub, $name, $type)
    {
        $replacements = [
            '{{ namespace }}' => 'App\\Enums',
            '{{ class }}' => class_basename($name),
            '{{ type }}' => $type ? ": {$type}" : '',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $stub);
    }

    protected function getPath($name)
    {
        $name = Str::studly($name);
        return app_path("Enums/{$name}.php");
    }
}
