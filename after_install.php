<?php

$info = file_exists (QERO_DIR .'/qero-info.json') ?
    json_decode (file_get_contents (QERO_DIR .'/qero-info.json'), true) : array ();

$info['scripts']['start'] = '"qero-packages'. str_replace (dirname (__DIR__, 3), '', __DIR__) .'/core/WinForms PHP.exe" "app/start.php"';

file_put_contents (QERO_DIR .'/qero-info.json', json_encode ($info, defined ('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : 0));

if (!file_exists (QERO_DIR .'/start.bat'))
    file_put_contents (QERO_DIR .'/start.bat', '@echo off
'. $info['scripts']['start']);

if (!is_dir (QERO_DIR .'/app'))
    mkdir (QERO_DIR .'/app');

if (!file_exists (QERO_DIR .'/app/start.php'))
    file_put_contents (QERO_DIR .'/app/start.php', '<?php
    
namespace VoidEngine;

const APP_DIR = __DIR__;
chdir (APP_DIR);

require __DIR__ .\'/../qero-packages/autoload.php\';

$parser = new VLFParser (__DIR__. \'/app.vlf\', [
    \'strong_line_parser\'            => false,
    \'ignore_postobject_info\'        => true,
    \'ignore_unexpected_method_args\' => true,

    \'use_caching\' => true,
    \'debug_mode\'  => false
]);

$objects = VLFInterpreter::run ($parser);

$APPLICATION->run ($objects[\'MainForm\']);
');

if (!file_exists (QERO_DIR .'/app/app.vlf'))
    file_put_contents (QERO_DIR .'/app/app.vlf', 'Form MainForm
    caption: \'Hello, World!\'
    size: [400, 300]
    startPosition: fspCenterScreen

    Button MainButton
    bounds: [8, 8, 120, 32]
    caption: \'Click Me!\'

    ClickEvent:^ function ($self)
        {
            pre ($self);
        }
');
