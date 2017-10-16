<?php

namespace Kirby\Users\User\Traits;

use Exception;
use Kirby\Users\Users;

trait Navigator
{

    protected $collection;

    public function collection(Users $collection = null)
    {
        if ($collection === null) {
            return $this->collection;
        }

        $this->collection = $collection;
        return $this;
    }

    public function indexOf()
    {
        return $this->collection()->indexOf($this);
    }

    public function prev()
    {
        return $this->collection()->nth($this->indexOf() - 1);
    }

    public function hasPrev(): bool
    {
        return $this->prev() !== null;
    }

    public function next()
    {
        return $this->collection()->nth($this->indexOf() + 1);
    }

    public function hasNext(): bool
    {
        return $this->next() !== null;
    }

    public function isFirst(): bool
    {
        return $this->collection()->first()->is($this);
    }

    public function isLast(): bool
    {
        return $this->collection()->last()->is($this);
    }

}