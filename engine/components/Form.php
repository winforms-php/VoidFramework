<?php

namespace VoidEngine;

class Form extends Control
{
	public $class = 'System.Windows.Forms.Form';

	public function get_icon ()
	{
		return new FormIcon ($this->selector);
	}
	
	public function get_clientSize ()
	{
		$size = $this->getProperty ('ClientSize');
		
		return [
			VoidEngine::getProperty ($size, 'Width'),
			VoidEngine::getProperty ($size, 'Height')
		];
	}
	
	public function set_clientSize ($size)
	{
		if (is_array ($size))
		{
			$clientSize = $this->getProperty ('ClientSize');

			VoidEngine::setProperty ($clientSize, 'Width', array_shift ($size));
			VoidEngine::setProperty ($clientSize, 'Height', array_shift ($size));

			$this->setProperty ('ClientSize', $clientSize);
		}

		else $this->setProperty ('ClientSize', EngineAdditions::uncoupleSelector ($size));
	}
}

class FormIcon extends Icon
{
    protected $formSelector;

    public function __construct (int $formSelector)
    {
        $this->formSelector = $formSelector;
    }

    public function loadFromFile (string $file)
	{
        $icon = VoidEngine::createObject ('System.Drawing.Icon', 'System.Drawing', $file);
        
        VoidEngine::setProperty ($this->formSelector, 'Icon', $icon);

		if (!isset ($this->selector))
		    $this->selector = $icon;
	}
}
