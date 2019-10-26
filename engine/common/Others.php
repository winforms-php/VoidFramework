<?php

namespace VoidEngine;

use VoidCore;

function dn (...$args): NetObject
{
    try
    {
        $object = new NetObject (...$args);
    }

    catch (\WinformsException $e)
    {
        if ((new NetObject ($e->getNetException ()))->toString () == 'System.MemberAccessException')
            throw $e;
        
        $object = new NetClass (...$args);
    }

    return $object;
}

function thread (callable $callable): NetObject
{
    return new NetObject (VoidCore::createThread ($callable));
}

function enum (string $baseType, string $value)
{
    try
    {
        return VoidCore::callMethod (VoidCore::getClass ('System.Enum'), ['parse', VC_OBJECT], VoidCore::typeof ($baseType), $value, true);
    }

    catch (\WinformsException $e)
    {
        return (new NetClass ($baseType))->$value;
    }
}

function getNetArray (string $type, array $items = []): NetObject
{
    $array = (new NetClass ('System.Array'))
        ->createInstance (VoidCore::typeof ($type), $size = sizeof ($items));

    for ($i = 0; $i < $size; ++$i)
        $array[$i] = array_shift ($items);
    
    return $array;
}

function dir_create (string $path, int $mode = 0777): void
{
    if (!is_dir ($path))
        mkdir ($path, $mode, true);
}

function dir_delete (string $path): bool
{
    if (!is_dir ($path))
        return false;

    foreach (array_slice (scandir ($path), 2) as $file)
        if (is_dir ($file = $path .'/'. $file))
        {
            dir_delete ($file);

            if (is_dir ($file))
                rmdir ($file);
        }

        else unlink ($file);

    rmdir ($path);

    return true;
}

function dir_clean (string $path): void
{
    dir_delete ($path);
    dir_create ($path);
}

function dir_copy (string $from, string $to): bool
{
    if (!is_dir ($from))
        return false;

    if (!is_dir ($to))
        dir_create ($to);

    foreach (array_slice (scandir ($from), 2) as $file)
        if (is_dir ($f = $from .'/'. $file))
            dir_copy ($f, $to .'/'. $file);

        else copy ($f, $to .'/'. $file);

    return true;
}

function argb (string $color)
{
    return (new NetClass ('System.Drawing.ColorTranslator'))->fromHtml ($color);
}

function replaceSl (string $string): string
{
    return str_replace ('\\', '/', $string);
}

function replaceSr (string $string): string
{
    return str_replace ('/', '\\', $string);
}

function basenameNoExt (string $path): string
{
    return pathinfo ($path, PATHINFO_FILENAME);
}

function file_ext (string $path): string
{
    return strtolower (pathinfo ($path, PATHINFO_EXTENSION));
}

function filepathNoExt (string $path): string
{
    return dirname ($path) .'/'. basenameNoExt ($path);
}

function pre (...$args): void
{
	message (sizeof ($args) < 2 ? current ($args) : $args);
}

function messageBox (string $message, string $caption = '', ...$args): int
{
    return (new MessageBox)->show ($message, $caption, ...$args);
}

function run (string $path, ...$args)
{
    return (new Process)->start ($path, ...$args);
}

function setTimer (int $interval, callable $function): Timer
{
    $timer = new Timer;
    $timer->interval  = $interval;
    $timer->tickEvent = fn ($self) => $function ($self);
    
	$timer->start ();
    
    return $timer;
}

function setTimeout (int $timeout, callable $function): Timer
{
    $timer = new Timer;
    $timer->interval  = $timeout;
    $timer->tickEvent = function ($self) use ($function)
    {
        $self->stop ();

        $function ($self);
    };
    
	$timer->start ();
    
	return $timer;
}

set_error_handler (function ($no, $str, $file, $line)
{
    // Мог ли я здесь сделать более адекватный код с использованием pow/sqrt? Да, мог
    // Почему не сделал? Скорость важнее
    static $errarr = [
        1     => 'E_ERROR',
        2     => 'E_WARNING',
        4     => 'E_PARSE',
        8     => 'E_NOTICE',
        16    => 'E_CORE_ERROR',
        32    => 'E_CORE_WARNING',
        64    => 'E_COMPILE_ERROR',
        128   => 'E_COMPILE_WARNING',
        256   => 'E_USER_ERROR',
        512   => 'E_USER_WARNING',
        1024  => 'E_USER_NOTICE',
        2048  => 'E_STRICT',
        4096  => 'E_RECOVERABLE_ERROR',
        8192  => 'E_DEPRECATED',
        16384 => 'E_USER_DEPRECATED'
    ];

    message ([
        'type'      => $errarr[$no],
        'text'      => $str,
        'file'      => $file,
        'line'      => $line
    ], 'PHP Script Error');
});
