# Настройка

После установки **VoidFramework** в папке проекта появятся файлы `start.bat` и `qero-info.json`. Первый является файлом для упрощённого запуска приложения. Второй же является основным файлом конфигурации **Qero** пакета. В нём вы можете указать упрощённые команды и зависимости приложения

## Пример конфигурации приложения

{% code-tabs %}
{% code-tabs-item title="qero-info.json" %}
```javascript
{
    "version": "1.0.0",
    "requires": [
        "winforms-php/VoidFramework",
        "KRypt0nn/Flurex"
    ],
    "scripts": {
        "start": "qero-packages/winforms-php/VoidFramework/core/VoidCore.exe app/start.php"
    }
}
```
{% endcode-tabs-item %}
{% endcode-tabs %}

Подробности о файле конфигурации **Qero** пакета вы можете узнать [здесь](https://github.com/KRypt0nn/Qero)

