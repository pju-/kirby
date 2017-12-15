<?php

namespace Kirby\Cms;

use Closure;
use Kirby\Util\Controller;
use Kirby\FileSystem\Folder;

/**
 * Manages and loads all collections
 * in site/collections, which can then
 * be reused in controllers, templates, etc
 *
 * This class is mainly used in the `$kirby->collection()`
 * method to provide easy access to registered collections
 *
 * @package   Kirby Cms
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      http://getkirby.com
 * @copyright Bastian Allgeier
 */
class Collections extends Object
{

    /**
     * Each collection is cached once it
     * has been called, to avoid further
     * processing on sequential calls to
     * the same collection.
     *
     * @var array
     */
    protected $cache = [];

    /**
     * Magic caller to enable something like
     * `$collections->myCollection()`
     *
     * @param  string $name
     * @param  array $arguments
     * @return Collection|null
     */
    public function __call(string $name, array $arguments = [])
    {
        return $this->get($name, ...$arguments);
    }

    /**
     * Loads a collection by name if registered
     *
     * @param string $name
     * @param array $data
     * @return Collection|null
     */
    public function get(string $name, array $data = [])
    {
        if (isset($this->cache[$name]) === true) {
            return $this->cache[$name];
        }

        $controller = new Controller($this->props[$name]);

        return $this->cache[$name] = $controller->call(null, $data);
    }

    /**
     * Loads collections from php files in a
     * given directory.
     *
     * @param  string $root
     * @return self
     */
    public static function load(string $root): self
    {
        $collections = [];
        $folder      = new Folder($root);

        foreach ($folder->files() as $file) {

            $collection = require $file;

            if (is_a($collection, Closure::class)) {
                $name = pathinfo($file, PATHINFO_FILENAME);
                $collections[$name] = $collection;
            }
        }

        return new static($collections);
    }

}