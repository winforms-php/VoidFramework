<?php
    
namespace VoidEngine;

# Объявление констант
const CORE_DIR = __DIR__ .'/../%FRAMEWORK_PATH%/core';
const APP_DIR  = __DIR__;

# Подгружаем PHP расширения
foreach (glob (CORE_DIR .'/ext/php_*.dll') as $ext)
	if (!extension_loaded ($ext = substr (basename ($ext), 4, -4)))
		dl ($ext);

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
