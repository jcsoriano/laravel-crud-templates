<?php

namespace JCSoriano\CrudTemplates\Generators;

use JCSoriano\CrudTemplates\DataObjects\Payload;

trait StandardGenerator
{
    /**
     * Returns the directory path where the file will be created.
     */
    abstract protected function directory(Payload $payload): string;

    /**
     * Returns the file name without the `.php` extension.
     */
    abstract protected function fileName(Payload $payload): string;

    /**
     * Returns the file type used in console logging (e.g., "Controller", "Model", "Policy").
     */
    abstract protected function fileType(Payload $payload): string;

    /**
     * Returns the path to the stub file (checks custom stubs directory first).
     */
    abstract protected function stubPath(Payload $payload): string;

    /**
     * Returns variables for stub placeholder replacement.
     */
    protected function variables(Payload $payload): array
    {
        return $payload->variables();
    }

    /**
     * Returns conditions for stub conditional logic.
     */
    protected function conditions(Payload $payload): array
    {
        return $payload->conditions();
    }

    /**
     * Returns whether to skip generation.
     */
    protected function shouldSkipGeneration(Payload $payload): bool
    {
        return false;
    }

    /**
     * Generates the file from the stub.
     */
    protected function generateFile(
        string $stubPath,
        string $directory,
        string $fileName,
        array $variables,
        array $conditions,
    ): void {
        LaravelStub::from($this->getStubPath($stubPath))
            ->to($directory)
            ->name($fileName)
            ->ext('php')
            ->replaces($variables)
            ->conditions($conditions)
            ->generate();
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