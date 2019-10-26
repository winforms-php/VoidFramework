<?php

namespace VoidEngine;

use VLF\{
    Parser as VLFParser,
    Interpreter as VLFInterpreter
};

use VLF\VST\{
    Parser as VSTParser,
    Interpreter as VSTInterpreter
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

# Парсим стили main.vst
VSTInterpreter::run (VSTParser::parse ('main.vst'));

# Парсим разметку app.vlf и запускаем приложение
$objects = VLFInterpreter::run (VLFParser::parse ('app.vlf'));

$APPLICATION->run ($objects['MainForm']);
