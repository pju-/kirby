<?php

namespace Kirby\Cms;

use Kirby\Form\Field;
use Kirby\Util\Dir;

trait AppPlugins
{

    protected $plugins;

    public function plugins(): array
    {
        if (is_array($this->plugins) === true) {
            return $this->plugins;
        }

        $root  = $this->root('plugins');
        $kirby = $this;

        $this->plugins = [];

        foreach (Dir::read($root) as $dirname) {

            if (is_dir($root . '/' . $dirname) === false) {
                continue;
            }

            $dir   = $root . '/' . $dirname;
            $entry = $dir . '/' . $dirname . '.php';

            if (file_exists($entry) === false) {
                continue;
            }

            $this->plugins[] = $dir;

            include_once $entry;

        }

        $this->registerContentFieldMethods();
        $this->registerFields();
        $this->registerHooks();
        $this->registerPageModels();

        return $this->plugins;

    }

    protected function registerContentFieldMethods()
    {
        $default = include static::$root . '/extensions/methods.php';
        $plugins = $this->registry->get('fieldMethod');

        // field methods
        ContentField::$methods = array_merge($default, $plugins);
    }

    protected function registerFields()
    {
        Field::$types = $this->get('field');
    }

    protected function registerHooks()
    {
        $this->hooks()->registerAll($this->get('hook'));
    }

    protected function registerPageModels()
    {
        // page models
        Page::$models = $this->get('pageModel');
    }

}
