<?php

namespace Kirby\Cms;

class EmailTemplate extends Template
{
    public function __construct(string $name, string $type = 'text')
    {
        parent::__construct($name, $type);
    }

    public function defaultType(): string
    {
        return 'text';
    }

    public function store(): string
    {
        return 'emails';
    }
}
