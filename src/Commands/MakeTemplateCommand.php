<?php

namespace JCSoriano\CrudTemplates\Commands;

use Illuminate\Console\Command;
use JCSoriano\CrudTemplates\Commands\Concerns\GeneratesPackageFiles;
use JCSoriano\CrudTemplates\Generators\Package\TemplateGenerator;

class MakeTemplateCommand extends Command
{
    use GeneratesPackageFiles;

    protected $signature = 'make:template {name} {--force}';

    protected $description = 'Create a new template class';

    public function handle(): int
    {
        // Generate file using generator
        app(TemplateGenerator::class)
            ->generate($this->buildPayload());

        // Add binding to published service provider
        $this->addBindingToServiceProvider(
            type: 'template',
            methodName: 'registerTemplates',
            namespace: 'App\\Templates',
            useClosure: true,
        );

        return self::SUCCESS;
    }
}
