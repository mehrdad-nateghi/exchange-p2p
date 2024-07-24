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

        // Dump the consolidated swagger.yaml file with proper formatting
        // Set the inline level to 10 and indent spaces to 2
        $consolidatedYaml = Yaml::dump($swagger, 10, 2, Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK | Yaml::DUMP_OBJECT_AS_MAP);
        file_put_contents(base_path('/swagger/swagger.yaml'), $consolidatedYaml);


        return collect($swagger)->toArray();
    }
}
