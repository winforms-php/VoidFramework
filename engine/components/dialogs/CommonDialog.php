<?php

namespace VoidEngine;

abstract class CommonDialog extends Component
{
    protected ?string $classname = 'System.Windows.Forms.CommonDialog';
	protected ?string $assembly  = 'System.Windows.Forms';

    public function execute (): int
    {
        return $this->callMethod ('ShowDialog');
    }
}
