<?php

namespace Kirby\Cms;

use Kirby\Collection\Finder;

class PagesFinder extends Finder
{

    public function findById(string $id)
    {
        $page = $this->collection()->get($id);

        if (!$page) {
            $page = $this->findByIdRecursive($id);
        }

        return $page;
    }

    public function findByIdRecursive(string $id, $startAt = null)
    {
        $path       = explode('/', $id);
        $collection = $this->collection();
        $item       = null;
        $query      = $startAt;

        foreach ($path as $key) {

            $query = ltrim($query . '/' . $key, '/');
            $item  = $collection->get($query) ?? null;

            if ($item === null) {
                return null;
            }

            $collection = $item->children();

        }

        return $item;
    }

    public function findByKey(string $key)
    {
        return $this->findById($key);
    }

}