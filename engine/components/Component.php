<?php

namespace VoidEngine;

use VoidCore;

abstract class Component extends NetObject
{
    public $helpStorage;

    protected ?string $classname;
    protected ?string $assembly;

    public function __construct (...$args)
    {
        parent::__construct ($this->classname, $this->assembly, ...$args);

        Components::add ($this);
    }

    public function dispose (): void
    {
        VoidCore::removeObjects ($this->selector);
        Components::remove ($this->selector);
    }

    public function __destruct ()
    {
        if (VoidCore::destructObject ($this->selector))
            $this->dispose ();
    }
}
