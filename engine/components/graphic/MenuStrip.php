<?php

namespace VoidEngine;

class MenuStrip extends Component
{
    protected ?string $classname = 'System.Windows.Forms.MenuStrip';
	protected ?string $assembly  = 'System.Windows.Forms';
}

class ContextMenuStrip extends Component
{
    protected ?string $classname = 'System.Windows.Forms.ContextMenuStrip';
	protected ?string $assembly  = 'System.Windows.Forms';
}

class ToolStripMenuItem extends Control
{
    protected ?string $classname = 'System.Windows.Forms.ToolStripMenuItem';
	protected ?string $assembly  = 'System.Windows.Forms';

    public function __construct (string $text = '')
    {
        parent::__construct ();

        $this->text = $text;
    }
}
