<?php

namespace VoidEngine;

class Components
{
    protected static array $objects = [];

    public static function add (NetObject $object): void
    {
        self::$objects[$object->selector] = \WeakReference::create ($object);
    }

    public static function get (int $selector): ?NetObject
    {
        if (!isset (self::$objects[$selector]))
            return null;
        
        $object = self::$objects[$selector]->get ();

        if ($object === null)
            self::remove ($selector);

        return $object;
    }

    public static function exists (int $selector): bool
    {
        return isset (self::$objects[$selector]);
    }

    public static function getObjects (): array
    {
        return self::$objects;
    }

    public static function remove (int $selector): void
    {
        unset (self::$objects[$selector]);
    }

    public static function clean (): void
    {
        foreach (self::$objects as $selector => $reference)
            if ($reference->get () === null)
                unset (self::$objects[$selector]);
    }
}

function _c (int $selector): ?NetObject
{
    return Components::get ($selector);
}

// TODO: поддержка многоуровневых ссылок вида родитель->родитель->объект
function c (string $name): ?NetObject
{
    if (($object = _c($name)) !== null)
        return $object;

    foreach (Components::getObjects () as $selector => $reference)
    {
        $object = $reference->get ();

        if ($object === null)
            continue;

        try
        {
            if ($object->name == $name)
                return $object;
        }

        catch (\WinformsException $e) {}
    }

    return null;
}
