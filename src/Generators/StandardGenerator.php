<?php

namespace JCSoriano\LaravelCrudTemplates\Generators;

use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;

trait StandardGenerator
{
    abstract protected function directory(Payload $payload): string;

    abstract protected function fileName(Payload $payload): string;

    abstract protected function fileType(Payload $payload): string;

    abstract protected function stubPath(Payload $payload): string;

    protected function variables(Payload $payload): array
    {
        return $payload->variables();
    }

    protected function conditions(Payload $payload): array
    {
        return $payload->conditions();
    }

    protected function shouldSkipGeneration(Payload $payload): bool
    {
        return false;
    }

    public function generate(Payload $payload): Payload
    {
        if ($this->shouldSkipGeneration($payload)) {
            return $payload;
        }

        $directory = $this->directory($payload);
        $this->createDirectoryIfNotExists($directory);

        $fileName = $this->fileName($payload);

        $fileType = $this->fileType($payload);

        // Check if file exists and return early if not forcing
        if ($this->checkIfFileExists($fileType, $directory, $fileName, $payload)) {
            return $payload;
        }

        $this->generateFile(
            stubPath: $this->stubPath($payload),
            directory: $directory,
            fileName: $fileName,
            variables: $this->variables($payload),
            conditions: $this->conditions($payload),
        );

        $this->logGeneratedFile($fileType, $directory, $fileName, $payload);

        return $payload;
    }
}