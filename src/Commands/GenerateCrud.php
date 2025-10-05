<?php

namespace JCSoriano\LaravelCrudStubs\Commands;

use Illuminate\Console\Command;
use JCSoriano\LaravelCrudStubs\DataObjects\Model;
use JCSoriano\LaravelCrudStubs\Exceptions\InvalidFieldsException;
use JCSoriano\LaravelCrudStubs\LaravelCrudStubs;
use JCSoriano\LaravelCrudStubs\Parsers\FieldsParser;
use JCSoriano\LaravelCrudStubs\Parsers\TableParser;

class GenerateCrud extends Command
{
    protected $signature = 'crud:generate
                            {model : The name of the model}
                            {--fields= : The fields to generate. Format: field1:type1,field2?:type2}
                            {--table= : The database table to generate the fields from}
                            {--type=api : The type of CRUD to generate}
                            {--options= : Other options to pass to the generator. Format: key1:value1,key2:value2}';

    protected $description = 'Generate CRUD files for a model';

    public function handle(): int
    {
        try {
            $modelPath = $this->argument('model');
            $fieldsString = $this->option('fields');
            $tableName = $this->option('table');
            $type = $this->option('type');
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

            // Get pipeline
            $pipelines = LaravelCrudStubs::getPipelines();

            if (! array_key_exists($type, $pipelines)) {
                $this->error("Unsupported pipeline type: {$type}");

                return self::FAILURE;
            }

            $pipelineClass = $pipelines[$type];
            $pipeline = new $pipelineClass(
                model: $model = new Model($modelPath),
                fields: $fields,
                components: $this->components,
                options: $this->parseOptions($optionsString),
            );

            // Generate files
            $this->info("Generating CRUD files for {$model->model()->studlyCase()}...");

            $pipeline->run();

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
}
