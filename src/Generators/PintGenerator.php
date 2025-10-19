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

        // Get tracked files
        $files = $payload->data['files'] ?? [];

        if (empty($files)) {
            $payload->components->info('No files to format.');

            return $payload;
        }

        // Run Pint on specific files
        $filesArg = implode(' ', array_map('escapeshellarg', $files));
        exec($pintPath.' '.$filesArg.' 2>&1', $output, $returnCode);

        $payload->components->info('Code formatting completed successfully.');

        return $payload;
    }
}
