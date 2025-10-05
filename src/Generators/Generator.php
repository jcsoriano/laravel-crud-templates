<?php

namespace JCSoriano\LaravelCrudStubs\Generators;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use JCSoriano\LaravelCrudStubs\DataObjects\Payload;

abstract class Generator
{
    abstract public function generate(Payload $payload): Payload;

    protected function createDirectoryIfNotExists(string $path): void
    {
        if (! File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }
    }

    protected function getStubPath(string $file): string
    {
        $customPath = base_path('stubs/'.$file);

        return file_exists($customPath)
            ? $customPath
            : __DIR__.'/../stubs/api/'.$file;
    }

    public function handle(Payload $payload, Closure $next)
    {
        return $next($this->generate($payload));
    }

    protected function printSuccess(string $type, string $directory, string $fileName, Payload $payload): void
    {
        $path = $directory.'/'.$fileName.'.php';

        $payload->components->info(sprintf('%s [%s] created successfully.', $type, $path));
    }

    protected function buildNamespace(string $namespace, Payload $payload): string
    {
        $folder = $payload->model->namespace();

        return $folder ? $namespace.'\\'.$folder : $namespace;
    }

    public function buildNamespaces(Collection $namespaces): string
    {
        return $namespaces->map(
            fn (string $namespace): string => 'use '.Str::finish($namespace, ';'),
        )->unique()->sort()->implode("\n");
    }
}
