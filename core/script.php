<?php

namespace VoidEngine;

const CORE_DIR = __DIR__;
chdir (CORE_DIR);

foreach (glob ('ext/php_*.dll') as $ext)
	if (!extension_loaded (substr (basename ($ext), 4, -4)))
		load_extension ($ext);

if (file_exists ('../engine/VoidEngine.php'))
	require '../engine/VoidEngine.php';

else message ('VoidEngine not founded');

$app = '../../app/start.php';

if (file_exists ($app))
	require $app;

else \VoidCore::callMethod (\VoidCore::createObject ('WinForms_PHP.DebugForm'), 'ShowDialog');
