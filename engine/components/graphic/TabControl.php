<?php

namespace VoidEngine;

class TabControl extends Control
{
    protected ?string $classname = 'System.Windows.Forms.TabControl';
	protected ?string $assembly  = 'System.Windows.Forms';
}

class TabPage extends Control
{
    protected ?string $classname = 'System.Windows.Forms.TabPage';
	protected ?string $assembly  = 'System.Windows.Forms';

    public function __construct (string $text = '')
    {
        parent::__construct ();

        $this->text = $text;
    }
}
