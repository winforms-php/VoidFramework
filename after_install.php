<?php

echo '    Configuring VoidFramework...'. PHP_EOL;

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

$info = file_exists (QERO_DIR .'/qero-info.json') ?
    json_decode (file_get_contents (QERO_DIR .'/qero-info.json'), true) : array ();

$info['scripts']['start'] = '"qero-packages'. str_replace (dirname (__DIR__, 3), '', __DIR__) .'/core/VoidCore.exe" "app/start.php"';

file_put_contents (QERO_DIR .'/qero-info.json', json_encode ($info, defined ('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : 0));
file_put_contents (QERO_DIR .'/start.bat', '@echo off
'. $info['scripts']['start']);

if (!is_dir (QERO_DIR .'/app'))
{
    echo '    Configuring application...'. PHP_EOL;

    mkdir (QERO_DIR .'/app');
    dir_copy (__DIR__ .'/app_bundle', QERO_DIR .'/app');
}

Qero\dir_delete (__DIR__ .'/app_bundle');
unlink (__FILE__);

echo '    Configuration completed'. PHP_EOL .
     '    Thank for installing KRypt0nn/VoidFramework!'. PHP_EOL . PHP_EOL;
