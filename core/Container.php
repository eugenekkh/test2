<?php

namespace Evgeny;

use Pimple\Container as BaseContainer;

class Container extends BaseContainer
{
    public function get($name)
    {
        return $this[$name];
    }
}
