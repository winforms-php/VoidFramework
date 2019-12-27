<?php

use const Qero\QERO_DIR;

use function Qero\{
    dir_delete,
    color
};

global $package;

echo color (' Configuring [yellow]VoidFramework[reset]...') . PHP_EOL;

function dir_copy (string $from, string $to): bool
{
    if (!is_dir ($from))
        return false;

    if (!is_dir ($to))
        mkdir ($to);

    foreach (array_slice (scandir ($from), 2) as $file)
        if (is_dir ($f = $from .'/'. $file))
            dir_copy ($f, $to .'/'. $file);

        else copy ($f, $to .'/'. $file);

    return true;
}

$package->scripts['start'] = '"qero-packages/winforms-php/VoidFramework/core/VoidCore.exe" "app/start.php"';

file_put_contents (QERO_DIR .'/start.bat', '@echo off
'. $package->scripts['start']);

if (!file_exists (QERO_DIR .'/app'))
{
    echo ' Configuring application...'. PHP_EOL;

    mkdir (QERO_DIR .'/app');
    dir_copy (__DIR__ .'/app_bundle', QERO_DIR .'/app');
}

dir_delete (__DIR__ .'/app_bundle');
unlink (__FILE__);

echo PHP_EOL . color (' [green]Configuration completed[reset]'. PHP_EOL .
                      ' Thank for installing [yellow]winforms-php/VoidFramework[reset]!') . PHP_EOL;
