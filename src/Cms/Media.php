<?php

namespace Kirby\Cms;

use Kirby\Data\Data;
use Kirby\Toolkit\F;
use Throwable;

class Media
{

    public static function link(Model $model, $filename)
    {
        // TODO: this should be refactored when users get normal files
        if (is_a($model, User::class) === true) {
            if ($filename === 'profile.jpg') {
                return $model->avatar()->publish()->url();
            }

            $file = $model->avatar();
        } else {
            if ($file = $model->file($filename)) {
                return $file->publish()->url();
            }

            $file = $model->file($options['filename']);
        }

        if (!$file) {
            return false;
        }

        try {
            $kirby   = $model->kirby();
            $url     = $model->mediaUrl() . '/' . $filename;
            $thumb   = $model->mediaRoot() . '/' . $filename;
            $options = Data::read($job = $thumb . '.json');

            if (empty($options) === true) {
                return false;
            }

            $kirby->thumb($file->root(), $thumb, $options);
            F::remove($job);
            return $url;
        } catch (Throwable $e) {
            return false;
        }

    }

}
