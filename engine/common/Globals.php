<?php

namespace VoidEngine;

register_superglobals ('APPLICATION', 'SCREEN');

$APPLICATION = new class
{
    public NetClass $application;
    public string $executablePath;
    
    public function __construct ()
    {
        $this->application    = new NetClass ('System.Windows.Forms.Application');
        $this->executablePath = $this->application->executablePath;
    }
    
    public function run ($form = null): void
    {
        if ($form instanceof NetObject)
            $this->application->run ($form->selector);
        
        elseif (is_int ($form) && \VoidCore::objectExists ($form))
            $this->application->run ($form);
        
        elseif ($form === null)
            $this->application->run ();

        else throw new \Exception ('$form param must be instance of "VoidEngine\NetObject" ("VoidEngine\Form"), be null or object selector');
    }
    
    public function restart (): void
    {
        $this->application->restart ();
        $this->close ();
    }
    
    public function close (): void
    {
        $this->application->exit ();
    }

    public function __call (string $name, array $args)
    {
        return $this->application->$name (...$args);
    }

    public function __get (string $name)
    {
        return $this->application->$name;
    }
};

$SCREEN = new class
{
    public NetClass $screen;
    
    public function __construct ()
    {
        $this->screen = new NetClass ('System.Windows.Forms.Screen');
    }
    
    public function __get ($name)
    {
        switch (strtolower ($name))
        {
            case 'width':
            case 'w':
                return $this->screen->primaryScreen->bounds->width;
            break;
            
            case 'height':
            case 'h':
                return $this->screen->primaryScreen->bounds->height;
            break;

            default:
                return $this->screen->$name;
            break;
        }
    }
    
    public function __debugInfo (): array
    {
        return [
            $this->w,
            $this->h
        ];
    }
};
