<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class SwaggerController extends Controller
{
    public function api(): string
    {
        $swagger_yaml[] = file_get_contents(base_path('/swagger/opener.yaml'));
        $directories = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(base_path('/swagger/api')));
        foreach ($directories as $directory) {
            if ($directory->getExtension() === 'yaml' && $directory->getFileName() !== 'opener.yaml') {
                $swagger_yaml[] = file_get_contents($directory);
            }
        }
        return join("\n", $swagger_yaml ?? []);
    }
}
