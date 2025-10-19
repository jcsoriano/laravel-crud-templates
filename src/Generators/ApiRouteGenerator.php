<?php

namespace JCSoriano\LaravelCrudTemplates\Generators;

use Illuminate\Support\Facades\File;
use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;

class ApiRouteGenerator extends Generator
{
    public function generate(Payload $payload): Payload
    {
        $model = $payload->model;
        $modelName = $model->model()->studlyCase();
        $modelKebabPlural = $model->model()->pluralKebabCase();

        $apiRoutesPath = base_path('routes/api.php');

        if (! File::exists($apiRoutesPath)) {
            // Create basic api.php file if it doesn't exist
            File::put($apiRoutesPath, "<?php\n\nuse Illuminate\Http\Request;\nuse Illuminate\Support\Facades\Route;\n\n");
        }

        $content = File::get($apiRoutesPath);

        // Add import at the top
        $controllerNamespace = $this->buildNamespace('App\\Http\\Controllers\\Api', $payload);
        $controllerImport = "use {$controllerNamespace}\\{$modelName}Controller;";

        if (! str_contains($content, $controllerImport)) {
            // Find the last use statement or add after <?php
            if (preg_match('/^use .+;$/m', $content)) {
                $content = preg_replace('/^(use .+;)$/m', "$1\n{$controllerImport}", $content, 1);
            } else {
                $content = preg_replace('/(<\?php\s*\n)/', "$1\n{$controllerImport}\n", $content);
            }
        }

        // Add route at the end
        $route = "\nRoute::apiResource('{$modelKebabPlural}', {$modelName}Controller::class);";

        if (! str_contains($content, $route)) {
            $content = rtrim($content)."\n{$route}\n";
        }

        File::put($apiRoutesPath, $content);

        $payload->components->info(sprintf('Route [%s] registered successfully.', $apiRoutesPath));

        $payload->data['files'][] = $apiRoutesPath;

        return $payload;
    }
}
