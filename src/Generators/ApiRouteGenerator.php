<?php

namespace JCSoriano\LaravelCrudTemplates\Generators;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;
use RuntimeException;

class ApiRouteGenerator extends Generator
{
    public function generate(Payload $payload): Payload
    {
        $model = $payload->model;
        $modelName = $model->model()->studlyCase();
        $modelKebabPlural = $model->model()->pluralKebabCase();

        $apiRoutesPath = base_path('routes/api.php');

        if (! File::exists($apiRoutesPath)) {
            $payload->components->info('Running `php artisan install:api` to create the API routes file. This may take a few seconds...');
            $exitCode = Artisan::call('install:api --without-migration-prompt --no-interaction');

            if ($exitCode !== 0) {
                throw new RuntimeException("Failed to install API routes. Please run 'php artisan api:install' manually.");
            }
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
        $route = "\n".$this->getStubContent('api.route.stub', [
            'MODEL_KEBAB_PLURAL' => $modelKebabPlural,
            'MODEL' => $modelName,
        ]);

        if (! str_contains($content, $route)) {
            $content = rtrim($content)."\n{$route}\n";
        }

        File::put($apiRoutesPath, $content);

        $payload->components->info(sprintf('Route [%s] registered successfully.', $apiRoutesPath));

        $payload->data['files'][] = $apiRoutesPath;

        return $payload;
    }
}
