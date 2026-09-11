<?php

namespace Phactory;

class Builder
{
    protected function toObject(mixed $name, mixed $values): mixed
    {
    }

    protected function saveObject(mixed $name, mixed $object): mixed
    {
    }
}

class Dependency
{
    protected function has(mixed $part, mixed $subject): bool
    {
    }
}

class Loader
{
    public function load(mixed $name): Factory
    {
    }
}

class Factory
{
    public function __construct(mixed $name, mixed $blueprint)
    {
    }
}
