<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Yaml\Yaml;

class SwaggerController extends Controller
{
    public function swagger(): array
    {
        $swagger = Yaml::parse(file_get_contents(base_path('/swagger/opener.yaml')));
        $directories = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(base_path('/swagger/api')));
        foreach ($directories as $directory) {
            if ($directory->getExtension() === 'yaml' && $directory->getFileName() !== 'opener.yaml') {
                $swagger['paths'] = array_merge_recursive($swagger['paths'] ?? [], Yaml::parse(file_get_contents($directory)) ?? []);
            }
        }

        // Create the consolidated swagger.yaml file
        $consolidatedYaml = Yaml::dump($swagger/*, 10, 2*/); // 10 is the inline level, 2 is the indentation
        file_put_contents(base_path('/swagger/swagger.yaml'), $consolidatedYaml);

        return collect($swagger)->toArray();
    }
}
