<?php

namespace VoidEngine;

class WebBrowser extends Control
{
    protected ?string $classname = 'System.Windows.Forms.WebBrowser';
    protected ?string $assembly  = 'System.Windows.Forms';

    public function browse (string $url): void
    {
        $this->callMethod ('Navigate', $url);
    }
}
