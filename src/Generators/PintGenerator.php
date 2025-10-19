<?php

namespace JCSoriano\LaravelCrudTemplates\Generators;

use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;

class PintGenerator extends Generator
{
    public function generate(Payload $payload): Payload
    {
        $pintPath = base_path('vendor/bin/pint');

        if (! file_exists($pintPath)) {
            return $payload;
        }

        // Run Pint with --dirty flag to only format changed files
        exec($pintPath.' --dirty 2>&1', $output, $returnCode);

        $payload->components->info('Code formatting completed successfully.');

        return $payload;
    }
}
