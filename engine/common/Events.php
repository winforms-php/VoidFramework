<?php

namespace VoidEngine;

use VoidCore;

class Events
{
    public static function setEvent (int $selector, string $eventName, callable $function): void
    {
        VoidCore::setEvent ($selector, $eventName, function ($sender, ...$args) use ($function)
		{
            try
			{
                foreach ($args as $id => $arg)
                    $args[$id] = EngineAdditions::coupleSelector ($arg);
                
                return $function (_c ($sender) ?: new NetObject ($sender), ...$args);
            }
            
			catch (\Throwable $e)
			{
                message ([
                    'type'  => get_class ($e),
                    'text'  => $e->getMessage (),
                    'file'  => $e->getFile (),
                    'line'  => $e->getLine (),
                    'code'  => $e->getCode (),
                    'trace' => $e->getTraceAsString ()
                ], 'PHP Critical Error');
            }
        });
    }

    public static function removeEvent (int $selector, string $eventName): void
    {
        VoidCore::removeEvent ($selector, $eventName);
    }
}
