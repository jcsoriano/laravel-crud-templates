<?php

namespace JCSoriano\LaravelCrudTemplates\Commands;

use Illuminate\Console\Command;
use JCSoriano\LaravelCrudTemplates\DataObjects\Model;
use JCSoriano\LaravelCrudTemplates\Exceptions\InvalidFieldsException;
use JCSoriano\LaravelCrudTemplates\Parsers\FieldsParser;
use JCSoriano\LaravelCrudTemplates\Parsers\TableParser;

class GenerateCrud extends Command
{
    protected $signature = 'crud:generate
                            {model : The name of the model}
                            {--fields= : The fields to generate. Format: field1:type1,field2?:type2}
                            {--table= : The database table to generate the fields from}
                            {--template=api : The CRUD template to generate}
                            {--skip= : Comma-separated list of generators to skip}
                            {--options= : Other options to pass to the generator. Format: key1:value1,key2:value2}
                            {--force : Overwrite existing files}';

    protected $description = 'Generate CRUD files for a model';

    public function handle(): int
    {
        try {
            $modelPath = $this->argument('model');
            $fieldsString = $this->option('fields');
            $tableName = $this->option('table');
            $template = $this->option('template');
            $optionsString = $this->option('options');

            // Parse fields
            $fieldsParser = new FieldsParser;
            $tableParser = new TableParser;

            $fields = collect();

            // Add fields from table first
            if ($tableName) {
                $tableFields = $tableParser->parse($tableName);
                $fields = $fields->merge($tableFields);
            }

            // Add/override with fields from --fields option
            if ($fieldsString) {
                $fieldsFromOption = $fieldsParser->parse($fieldsString);

                // Merge fields, with fields from option overriding table fields
                foreach ($fieldsFromOption as $fieldFromOption) {
                    $existingIndex = $fields->search(function ($field) use ($fieldFromOption) {
                        return $field->name->name === $fieldFromOption->name->name;
                    });

                    if ($existingIndex !== false) {
                        $fields[$existingIndex] = $fieldFromOption;
                    } else {
                        $fields->push($fieldFromOption);
                    }
                }
            }

            // Resolve template class from container
            $templateClass = app("laravel-crud-templates::template::{$template}");
            $options = $this->parseOptions($optionsString);
            $skip = $this->parseSkip($this->option('skip'));

            $templateInstance = new $templateClass(
                model: $model = new Model($modelPath),
                fields: $fields,
                components: $this->components,
                force: $this->option('force'),
                table: $tableName,
                options: $options,
                skip: $skip,
            );

            // Generate files
            $this->info("Generating CRUD files for {$model->model()->studlyCase()}...");

            $templateInstance->run();

            $this->info('CRUD files generated successfully!');

            return self::SUCCESS;
        } catch (InvalidFieldsException $e) {
            $this->error("Invalid fields: {$e->getMessage()}");

            return self::FAILURE;
        }
    }

    protected function parseOptions(?string $optionsString): array
    {
        if (empty($optionsString)) {
            return [];
        }

        $options = [];
        $pairs = explode(',', $optionsString);

        foreach ($pairs as $pair) {
            $parts = explode(':', trim($pair), 2);

            if (count($parts) === 2) {
                $options[trim($parts[0])] = trim($parts[1]);
            }
        }

        return $options;
    }

    protected function parseSkip(?string $skipString): array
    {
        if (empty($skipString)) {
            return [];
        }

        return array_map('trim', explode(',', $skipString));
    }
}
