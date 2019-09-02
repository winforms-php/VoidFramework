<?php

namespace VoidEngine;

# Объявление констант
const APP_DIR = __DIR__;

$package = json_decode (@file_get_contents (dirname (__DIR__) .'/qero-packages/packages.json'), true);

define ('VoidEngine\CORE_DIR', isset ($package['github:winforms-php/VoidFramework']['basefolder']) ?
	dirname (__DIR__) .'/qero-packages/winforms-php/VoidFramework/'. $package['github:winforms-php/VoidFramework']['basefolder'] .'/core' : __DIR__);

# Подгружаем PHP расширения
foreach (glob (CORE_DIR .'/ext/php_*.dll') as $ext)
	if (!extension_loaded (substr (basename ($ext), 4, -4)))
		load_extension ($ext);

# Подгружаем Qero-пакеты
require __DIR__ .'/../qero-packages/autoload.php';

chdir (APP_DIR); // Меняем стандартную директорию на директорию приложения

# Парсим app.vlf
$parser = new VLFParser (__DIR__. '/app.vlf', [
    'strong_line_parser'            => false,
    'ignore_postobject_info'        => true,
    'ignore_unexpected_method_args' => true,

    'use_caching' => false,
    'debug_mode'  => false
]);

# Запускаем приложение
$objects = VLFInterpreter::run ($parser);
$APPLICATION->run ($objects['MainForm']);
