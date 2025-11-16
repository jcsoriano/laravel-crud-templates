<?php

namespace JCSoriano\CrudTemplates\FieldTypes\Traits;

trait ParsesRelatedModel
{
    private function getModelClass(): string
    {
        return $this->field->relatedClass('App\\Models');
    }

    private function getModelName(): string
    {
        $field = $this->field;

        return $field->model?->model()->singularStudlyCase()
            ?? $field->name->singularStudlyCase();
    }
}
