<?php

namespace VoidEngine;

class MessageBox extends NetClass
{
    protected ?string $classname = 'System.Windows.Forms.MessageBox';
    protected ?string $assembly  = 'System.Windows.Forms';
    
    public function __construct ()
    {
        parent::__construct ($this->classname, $this->assembly);
    }
}
