<?php

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

$info['scripts']['start'] = '"qero-packages'. str_replace (dirname (__DIR__, 3), '', __DIR__) .'/core/WinForms PHP.exe" "app/start.php"';

file_put_contents (QERO_DIR .'/qero-info.json', json_encode ($info, defined ('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : 0));

if (!file_exists (QERO_DIR .'/start.bat'))
    file_put_contents (QERO_DIR .'/start.bat', '@echo off
'. $info['scripts']['start']);

if (!is_dir (QERO_DIR .'/app'))
{
    mkdir (QERO_DIR .'/app');
    
    dir_copy (__DIR__ .'/app_bundle', QERO_DIR .'/app');

    // Обновление переменных
    file_put_contents (QERO_DIR .'/app/start.php', str_replace ([
        '%FRAMEWORK_PATH%'
    ], [
        'qero-packages'. str_replace (dirname (__DIR__, 3), '', __DIR__)
    ], file_get_contents (QERO_DIR .'/app/start.php')));
}

Qero\dir_delete (__DIR__ .'/app_bundle');
unlink (__FILE__);
