<?php

namespace VoidEngine;

use VoidCore;

class Process extends Component
{
	protected ?string $classname = 'System.Diagnostics.Process';
	protected ?string $assembly  = 'System';

	public function __construct (int $pid = null)
	{
        $this->selector = VoidCore::getClass ($this->classname, $this->assembly);

		if ($pid !== null)
            $this->selector = $pid == getmypid () ?
                VoidCore::callMethod ($this->selector, 'GetCurrentProcess') :
                VoidCore::callMethod ($this->selector, 'GetProcessById', $pid);

		Components::addComponent ($this->selector, $this);
	}
	
	public static function getProcessById (int $pid)
	{
		return new self ($pid);
	}
	
	public static function getCurrentProcess ()
	{
		return new self (getmypid ());
	}
}
