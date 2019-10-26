<?php

namespace VoidEngine;

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * @package     VoidEngine
 * @copyright   2018 - 2019 Podvirnyy Nikita (KRypt0n_) & Andrey Kusov
 * @license     GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.html>
 * @license     Enfesto Studio Group license <https://vk.com/topic-113350174_36400959>
 * @see         license.txt for details
 * @author      Podvirnyy Nikita (KRypt0n_) & Andrey Kusov
 * 
 * @version     4.0.0rc1
 * 
 * Contacts:
 *
 * Podvirnyy Nikita:
 * Email: <suimin.tu.mu.ga.mi@gmail.com>
 * VK:    vk.com/technomindlp
 *        vk.com/hphp_convertation
 * 
 * Andrey Kusov:
 * VK: vk.com/postmessagea
 * 
 */

// https://www.php.net/manual/en/function.version-compare.php

const ENGINE_VERSION = '4.0.0rc1';
const ENGINE_DIR = __DIR__;

chdir (ENGINE_DIR);

require 'common/Events.php';
require 'common/EngineInterfaces.php';
require 'common/Globals.php';
require 'common/Constants.php';
require 'common/Components.php';
require 'common/Others.php';

define ('VoidEngine\CORE_VERSION', $APPLICATION->productVersion);

require 'components/Component.php';
require 'components/Control.php';

foreach (glob ('components/*/*.php') as $name)
    require $name;

if (file_exists ('extensions'))
    foreach (scandir ('extensions') as $ext)
        if (file_exists ($ext = 'extensions/'. $ext .'/main.php'))
            require $ext;
