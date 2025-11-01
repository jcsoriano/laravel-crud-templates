<?php

namespace JCSoriano\CrudTemplates\Generators;

use JCSoriano\CrudTemplates\DataObjects\Payload;

class ModelGenerator extends Generator
{
    use StandardGenerator;

    protected function directory(Payload $payload): string
    {
        $namespace = $payload->model->namespace();

        return app_path('Models/'.str_replace('\\', '/', $namespace));
    }

    protected function fileName(Payload $payload): string
    {
        return $payload->model->model()->studlyCase();
    }

    protected function fileType(Payload $payload): string
    {
        return 'Model';
    }

    protected function stubPath(Payload $payload): string
    {
        return 'api/model.stub';
    }

    protected function variables(Payload $payload): array
    {
        $fillableOutput = $this->print('fillable', $payload);
        $castsOutput = $this->print('casts', $payload);
        $relationsOutput = $this->print('relations', $payload);

        // Collect namespaces from printers
        $namespaces = collect([
            'Illuminate\Database\Eloquent\Factories\HasFactory',
            'Illuminate\Database\Eloquent\Model',
            'Illuminate\Database\Eloquent\SoftDeletes',
        ])
            ->merge($castsOutput->namespaces)
            ->merge($relationsOutput->namespaces);

        return [
            ...$payload->variables(),
            'FILLABLE' => $fillableOutput->output,
            'CASTS' => $castsOutput->output,
            'RELATIONS' => $relationsOutput->output,
            'NAMESPACES' => $this->buildNamespaces($namespaces),
        ];
    }
}
