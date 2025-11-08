<?php

namespace JCSoriano\CrudTemplates\Commands;

use Illuminate\Console\Command;
use JCSoriano\CrudTemplates\Commands\Concerns\GeneratesPackageFiles;
use JCSoriano\CrudTemplates\Generators\Package\GeneratorGenerator;

class MakeGeneratorCommand extends Command
{
    use GeneratesPackageFiles;

    protected $signature = 'make:generator {name} {--custom} {--standard} {--force}';

    protected $description = 'Create a new generator class';

    public function handle(): int
    {
        // Default to --standard if no flag provided
        $isCustom = $this->option('custom');
        $isStandard = $this->option('standard');

        if (! $isCustom && ! $isStandard) {
            $isStandard = true;
        }

        $stubFile = $isCustom ? 'generator.custom.stub' : 'generator.standard.stub';

        // Create payload
        $payload = $this->buildPayload(['stub' => $stubFile]);

        // Generate file using generator
        app(GeneratorGenerator::class)->generate($payload);

        // Add binding to published service provider
        $this->addBindingToServiceProvider(
            type: 'generator',
            methodName: 'registerGenerators',
            namespace: 'App\\Generators',
            suffix: 'Generator',
        );

        return self::SUCCESS;
    }
}
