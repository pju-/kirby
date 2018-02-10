<?php

namespace Kirby\Form;

use Exception;
use Closure;
use Kirby\Collection\Collection;

class Fields extends Collection
{

    /**
     * Internal setter for each object in the Collection.
     * This takes care of Object validation and of setting
     * the collection prop on each object correctly.
     *
     * @param string $id
     * @param object $object
     */
    public function __set(string $name, $field)
    {
        if (is_array($field)) {
            // use the array key as name if the name is not set
            $field['name'] = $field['name'] ?? $name;
            $field = Field::factory($field);
        }

        if (is_a($field, Field::class) === false) {
            throw new Exception('Invalid Field object in Fields collection');
        }

        return parent::__set($field->name(), $field);
    }

    public function toArray(Closure $map = null): array
    {
        $array = [];

        foreach ($this as $field) {
            $array[$field->name()] = $field->toArray();
        }

        return $array;
    }

    public function toOptions(): array
    {
        $array = [];

        foreach ($this as $field) {
            $options = $field->toArray();
            unset($options['value']);

            $array[$field->name()] = $options;
        }

        return $array;
    }

}