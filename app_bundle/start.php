<?php

namespace VoidEngine;

# Поиск basefolder VoidFramework'а
// Да, это выглядит плохо. Вероятно в будущем я сделаю нормальную систему в Qero, без basefolder'ов
$package = json_decode (file_get_contents (dirname (__DIR__) .'/qero-packages/packages.json'), true)['github:KRypt0nn/VoidFramework']['basefolder'];

# Объявление констант
const APP_DIR  = __DIR__;
define ('VoidEngine\CORE_DIR', dirname (__DIR__) .'/qero-packages/KRypt0nn/VoidFramework/'. $package .'/core');

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
