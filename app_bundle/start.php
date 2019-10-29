<?php

namespace VoidEngine;

use VLF\{
    Parser,
    Interpreter
};

# Объявление констант
const APP_DIR = __DIR__;

define ('VoidEngine\CORE_DIR', dirname (__DIR__) .'/qero-packages/winforms-php/VoidFramework/core');

# Подгружаем PHP расширения
foreach (glob (CORE_DIR .'/ext/php_*.dll') as $ext)
	if (!extension_loaded (substr (basename ($ext), 4, -4)))
		load_extension ($ext);

# Подгружаем Qero-пакеты
require __DIR__ .'/../qero-packages/autoload.php';

chdir (APP_DIR); // Меняем стандартную директорию на директорию приложения

# Парсим разметку app.vlf и запускаем приложение
$objects = Interpreter::run (Parser::parse ('app.vlf'));

$APPLICATION->run ($objects['MainForm']);
