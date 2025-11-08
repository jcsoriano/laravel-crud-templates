<?php

namespace JCSoriano\CrudTemplates\Commands\Concerns;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use JCSoriano\CrudTemplates\DataObjects\Model;
use JCSoriano\CrudTemplates\DataObjects\Name;
use JCSoriano\CrudTemplates\DataObjects\Payload;

trait GeneratesPackageFiles
{
    protected function buildPayload(array $options = []): Payload
    {
        return new Payload(
            components: $this->components,
            model: new Model($this->argument('name')),
            fields: collect(),
            force: $this->option('force') ?? false,
            options: $options,
        );
    }

    protected function addBindingToServiceProvider(
        string $type,
        string $methodName,
        string $namespace,
        string $bindingKeyCase = 'kebabCase',
        bool $useClosure = false,
        string $suffix = ''
    ): void {
        $name = new Name($this->argument('name'));
        $className = $name->studlyCase().$suffix;
        $bindingKey = $name->$bindingKeyCase();
        $providerPath = app_path('Providers/CrudTemplatesServiceProvider.php');

        if (! File::exists($providerPath)) {
            $this->call('vendor:publish', ['--tag' => 'crud-templates-provider']);

            ServiceProvider::addProviderToBootstrapFile(\App\Providers\CrudTemplatesServiceProvider::class);
        }

        $content = File::get($providerPath);

        // Check if binding already exists
        if (str_contains($content, "crud-templates::{$type}::{$bindingKey}")) {
            $this->components->info('Binding already exists in service provider.');

            return;
        }

        // Find the register method and add the binding
        $pattern = "/(protected function {$methodName}\(\): void\s*\{[^}]*)(\/\/[^\n]*\n\s*)\}/s";

        $bindingValue = $useClosure
            ? "fn () => \\{$namespace}\\{$className}::class"
            : "\\{$namespace}\\{$className}::class";

        $binding = "    \$this->app->bind(\"crud-templates::{$type}::{$bindingKey}\", {$bindingValue});\n";

        $replacement = "$1$2{$binding}    }";

        $newContent = preg_replace($pattern, $replacement, $content);

        if ($newContent !== $content) {
            File::put($providerPath, $newContent);
            $this->components->info('Binding added to CrudTemplatesServiceProvider.');
        }
    }
}
