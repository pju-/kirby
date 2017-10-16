<?php

use Kirby\Toolkit\Str;

return [
    'setup' => function ($model, $params): array {

        $options = [];

        foreach ($params['options'] as $value => $text) {
            $options[] = [
                'value' => $value,
                'text'  => $text
            ];
        }

        return [
            'options' => $options
        ];

    },
    'read' => function ($model, $key, $value, $options): array {

        if (is_string($value) === true) {
            return Str::split($value, ',');
        }

        if (is_array($value)) {
            return $value;
        }

        return [];

    },
    'write' => function ($model, $field, $value) {
        return implode(', ', (array)$value);
    }
];