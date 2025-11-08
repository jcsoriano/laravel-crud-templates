<?php

namespace JCSoriano\CrudTemplates\Commands;

use Illuminate\Console\Command;
use JCSoriano\CrudTemplates\Commands\Concerns\GeneratesPackageFiles;
use JCSoriano\CrudTemplates\Generators\Package\PrinterGenerator;

class MakePrinterCommand extends Command
{
    use GeneratesPackageFiles;

    protected $signature = 'make:printer {name} {--force}';

    protected $description = 'Create a new printer class';

    public function handle(): int
    {
        // Generate file using generator
        app(PrinterGenerator::class)
            ->generate($this->buildPayload());

        // Add binding to published service provider
        $this->addBindingToServiceProvider(
            type: 'printer',
            methodName: 'registerPrinters',
            namespace: 'App\\Printers',
            suffix: 'Printer',
        );

        return self::SUCCESS;
    }
}
