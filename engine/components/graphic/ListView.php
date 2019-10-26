<?php

namespace VoidEngine;

class ListView extends Control
{
    protected ?string $classname = 'System.Windows.Forms.ListView';
	protected ?string $assembly  = 'System.Windows.Forms';
}

class ListViewItem extends Control
{
    protected ?string $classname = 'System.Windows.Forms.ListViewItem';
	protected ?string $assembly  = 'System.Windows.Forms';

    public function __construct (string $text = '')
    {
        parent::__construct ();

        $this->text = $text;
    }
}

class ColumnHeader extends Control
{
    protected ?string $classname = 'System.Windows.Forms.ColumnHeader';
	protected ?string $assembly  = 'System.Windows.Forms';

    public function __construct (string $text = '')
    {
        parent::__construct ();

        $this->text = $text;
    }
}

class ListViewGroup extends Control
{
    protected ?string $classname = 'System.Windows.Forms.ListViewGroup';
	protected ?string $assembly  = 'System.Windows.Forms';

    public function __construct (string $text = '')
    {
        parent::__construct ();

        $this->header = $text;
    }
}
