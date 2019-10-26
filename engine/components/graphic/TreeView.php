<?php

namespace VoidEngine;

class TreeView extends Control
{
    protected ?string $classname = 'System.Windows.Forms.TreeView';
	protected ?string $assembly  = 'System.Windows.Forms';

    public function get_path (): ?string
    {
        try
        {
            $node = $this->selectedNode;
        }

        catch (\WinformsException $e)
        {
            return null;
        }
        
        return $node->fullPath;
    }
}

class TreeNode extends Control
{
    protected ?string $classname = 'System.Windows.Forms.TreeNode';
	protected ?string $assembly  = 'System.Windows.Forms';

    public function __construct (string $text = '')
    {
        parent::__construct ();

        $this->text = $text;
    }

    public function get_path ()
    {
        return $this->getProperty ('FullPath');
    }
}
