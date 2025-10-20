<?php

namespace JCSoriano\LaravelCrudTemplates\FieldTypes\Traits;

trait ParsesRelatedModel
{
    private function getModelClass(): string
    {
        return $this->field->relatedClass('App\\Models');
    }

    private function getModelName(): string
    {
        return $this->field->model->model()->studlyCase();
    }
}
