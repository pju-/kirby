<?php

use Kirby\FileSystem\Folder;
use Kirby\Data\Data;

return [
    'pattern' => 'panel/languages',
    'action'  => function () {

        $folder = new Folder($this->app()->root('panel') . '/assets/languages');
        $result = [];

        foreach ($folder->folders() as $root) {

            $locale         = basename($root);
            $file           = $root . '/package.json';
            $json           = Data::read($file);
            $json['locale'] = $locale;

            $result[] = $this->output('panel/language', $json);

        }

        return $result;

    }
];