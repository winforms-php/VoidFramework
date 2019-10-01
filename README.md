---
description: >-
  VoidFramework - инструмент для создания графических приложений для Windows на
  базе .NET Framework и PHP
---

# Начало работы

 [![](https://scrutinizer-ci.com/g/winforms-php/VoidFramework/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/winforms-php/VoidFramework/?branch=master) [![](https://scrutinizer-ci.com/g/winforms-php/VoidFramework/badges/build.png?b=master)](https://scrutinizer-ci.com/g/winforms-php/VoidFramework/build-status/master) [![](https://scrutinizer-ci.com/g/winforms-php/VoidFramework/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)

## Системные требования

| Требование | Значение |
| :--- | :--- |
| Версия **Windows** | ≥ **7** |
| Версия **.NET Framework** | ≥ **4.5.2** |
| Версия **Visual C++ Redistributable** | **2017** |

## Установка

{% tabs %}
{% tab title="Qero" %}
```text
php Qero.phar install winforms-php/VoidFramework
```
{% endtab %}

{% tab title="Ручная установка" %}
Скачайте **GitHub** репозиторий проекта

![](.gitbook/assets/screenshot_1.png)

Распакуйте архив с **VoidFramework** в папку проекта

![](.gitbook/assets/screenshot_1%20%281%29.png)

Переместите папку `app_bundle` на уровень ниже распакованной папки **VoidFramework**'а и переименуйте её в `app`

![](.gitbook/assets/screenshot_1%20%282%29.png)
{% endtab %}
{% endtabs %}

{% hint style="info" %}
Для установки настоятельно рекомендуется использовать [**Qero**](https://github.com/KRypt0nn/Qero). В дальнейшем все действия будут рассматриваться с учётом того, что **VoidFramework** был установлен именно таким образом
{% endhint %}

## Использование

После установки в проекте появится папка `app`. В ней будет располагаться будущее **VoidFramework**-приложение. В качестве точки входа используется файл `app/start.php`

Для запуска приложения вы можете прописать команду

```text
php Qero.phar start
```

или запустить файл `start.bat`

