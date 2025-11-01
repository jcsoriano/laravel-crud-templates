<?php

namespace JCSoriano\CrudTemplates\Commands;

use Illuminate\Console\Command;
use JCSoriano\CrudTemplates\Commands\Concerns\GeneratesPackageFiles;
use JCSoriano\CrudTemplates\Generators\Package\FieldTypeGenerator;

class MakeFieldTypeCommand extends Command
{
    use GeneratesPackageFiles;

    protected $signature = 'make:field-type {name} {--force}';

    protected $description = 'Create a new field type class';

    public function handle(): int
    {
        // Generate file using generator
        app(FieldTypeGenerator::class)
            ->generate($this->buildPayload());

        // Add binding to published service provider
        $this->addBindingToServiceProvider(
            type: 'field-type',
            methodName: 'registerFieldTypes',
            namespace: 'App\\FieldTypes',
            bindingKeyCase: 'camelCase',
            useClosure: true,
        );

        return self::SUCCESS;
    }
}
