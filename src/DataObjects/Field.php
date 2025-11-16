<?php

namespace JCSoriano\CrudTemplates\DataObjects;

class Field
{
    /** @param  class-string  $typeClass */
    public function __construct(
        public protected(set) Name $name,
        public protected(set) bool $required,
        public protected(set) string $typeClass,
        public protected(set) array $options = [],
        public protected(set) ?Model $model = null,
    ) {}

    public function relatedClass(string $basePath): string
    {
        if ($this->model) {
            $model = $this->model;

            return collect([
                $basePath,
                $model->namespace(),
                $model->model()->singularStudlyCase(),
            ])->filter()->join('\\');
        }

        return $basePath.'\\'.$this->name->singularStudlyCase();
    }
}
