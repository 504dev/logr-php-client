<?php

namespace Php\Package;

class User
{
    private $name;

    public function __construct($name, $children = [])
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
}