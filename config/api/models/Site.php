<?php

use Kirby\Cms\Form;
use Kirby\Cms\Site;

/**
 * Site
 */
return [
    'default' => function () {
        return $this->site();
    },
    'fields' => [
        'blueprint' => function (Site $site) {
            return $site->blueprint();
        },
        'children' => function (Site $site) {
            return $site->children();
        },
        'content' => function (Site $site) {
            return Form::for($site)->values();
        },
        'files' => function (Site $site) {
            return $site->files();
        },
        'title' => function (Site $site) {
            return $site->title()->value();
        },
        'url' => function (Site $site) {
            return $site->url();
        },
    ],
    'type' => Site::class,
    'views' => [
        'default' => [
            'content',
            'title',
            'url'
        ],
        'compact' => [
            'title',
            'url'
        ]
    ]
];