<?php

global $controller;

$info = file_exists (QERO_DIR .'/qero-info.json') ?
    json_decode (file_get_contents (QERO_DIR .'/qero-info.json'), true) : array ();

$info['scripts']['start'] = '"'. str_replace (QERO_DIR, '', __DIR__) .'/core/WinForms PHP.exe" "app/start.php"';

file_put_contents (QERO_DIR .'/qero-info.json', json_encode ($info, defined ('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : 0));

if (!is_dir (QERO_DIR .'/app'))
    mkdir (QERO_DIR .'/app');

if (!file_exists (QERO_DIR .'/app/start.php'))
    file_put_contents (QERO_DIR .'/app/start.php', '<?php
    
namespace VoidEngine;

require \'../qero-packages/autoload.php\';

pre (123);
');
