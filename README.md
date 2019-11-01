<h1 align="center">VoidFramework</h1>

<p align="center">
    <a href="https://scrutinizer-ci.com/g/winforms-php/VoidFramework/?branch=dev" target="_blank"><img src="https://scrutinizer-ci.com/g/winforms-php/VoidFramework/badges/quality-score.png?b=dev"></a>
    <a href="https://scrutinizer-ci.com/g/winforms-php/VoidFramework/build-status/dev" target="_blank"><img src="https://scrutinizer-ci.com/g/winforms-php/VoidFramework/badges/build.png?b=dev"></a>
    <a href="https://scrutinizer-ci.com/code-intelligence" target="_blank"><img src="https://scrutinizer-ci.com/g/winforms-php/VoidFramework/badges/code-intelligence.svg?b=dev"></a>
    <a href="license.txt"><img src="https://img.shields.io/badge/license-GPL%20v3-blue.svg"></a>
</p>

<p align="center"><b>VoidFramework</b> — фреймворк для разработки графических приложений для <b>Windows</b> на базе <b>.NET Framework</b> и <b>PHP</b></p><br>

## Системные требования

Требование | Значение
------------- | -------------
Версия **Windows** | ≥ **7**
Версия **.NET Framework** | ≥ **4.5.2** ([4.8](http://go.microsoft.com/fwlink/?LinkId=2085155))
Версия **Visual C++ Redistributable** | **2017** ([x64](https://aka.ms/vs/16/release/VC_redist.x64.exe) / [x86](https://aka.ms/vs/16/release/VC_redist.x86.exe))

## Установка (Qero)

```cmd
php Qero.phar install winforms-php/VoidFramework
```

> Qero: [тык](https://github.com/KRypt0nn/Qero)

## Использование

После установки создастся папка **app** рядом с папкой **qero-packages**. В ней размещается само приложение **VoidFramework**. В качестве точки входа используется файл **start.php**

Для запуска приложения вы можете использовать команду

```cmd
php Qero.phar start
```

запустить файл **start.bat** или создать какой-нибудь ярлык. Это не суть важно. Запуск приложения происходит через файл **%VoidFramework%/core/VoidCore.exe** с аргументом в виде пути к файлу точки входа *(подробнее в **start.bat**)*

Авторы: [Подвирный Никита](https://vk.com/technomindlp) и [Андрей Кусов](https://vk.com/postmessagea)
