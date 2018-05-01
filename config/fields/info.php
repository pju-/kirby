<?php

use Kirby\Util\I18n;

return [
    'save' => false,
    'props' => [
        'text' => function ($value = null) {
            return kirbytext(I18n::translate($value, $value));
        },
    ],
];
