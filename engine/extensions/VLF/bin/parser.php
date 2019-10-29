<?php

namespace VLF;

/**
 * Парсер VLF разметки
 */
class Parser
{
    // Разделитель строк
    public static string $divider = "\n";

    /**
     * Парсер AST дерева из VLF разметки
     * 
     * @param string $vlf - VLF разметка
     * 
     * @return AST - возвращает AST дерево разметки
     */
    public static function parse (string $vlf): AST
    {
        $tree    = new AST;
        $objects = new Stack;

        if (file_exists ($vlf))
            $vlf = file_get_contents ($vlf);

        $lines   = explode (self::$divider, $vlf);
        $skip_at = -1;

        foreach ($lines as $line_num => $line)
        {
            // \VoidEngine\pre ($line_num .', '. ($skip_at > $line_num ? 'skip' : 'not skip') .': '. $line);

            if ($skip_at > $line_num || !self::filter ($line))
                continue;

            $height = self::getHeight ($line);
            $words  = array_filter (explode (' ', $line), 'VLF\Parser::filter');
            $poped  = false;

            # Очищаем стек объектов
            while ($objects->size () > 0)
                if ($objects->current ()->height >= $height)
                {
                    $objects->pop ();
                    
                    $poped = true;
                }

                else break;

            # Создаём новую ссылку на объект
            if ($poped && $objects->size () > 0)
            {
                $object = $objects->pop ();

                $objects->push (new Node (array_merge ($object->export (), ['nodes' => []])));
                $tree->push ($objects->current ());
            }

            /**
             * Импорт таблиц стилей
             */
            if ($words[0] == 'import')
            {
                $imports = substr ($line, strlen ($words[0]));
                $parsed  = self::parseSubtext ($lines, $line_num, $height);

                $imports .= $parsed[0];
                $skip_at  = $parsed[1];

                $imports = self::filter ($imports) ?
                    array_map ('trim', self::parseArguments ($imports)) : [];

                $tree->push (new Node ([
                    'type'   => STYLES_IMPORTING,
                    'line'   => $line,
                    'words'  => $words,
                    'height' => $height,

                    'args' => [
                        'imports' => $imports
                    ]
                ]));
            }

            /**
             * Комментарии
             */
            elseif ($words[0][0] == '#')
            {
                /**
                 * Обработка многострочных комментариев
                 */
                if (isset ($words[0][1]))
                {
                    if ($words[0][1] == '^')
                        $skip_at = self::parseSubtext ($lines, $line_num, $height)[1];

                    else throw new \Exception ('Unknown char founded after comment definition at line '. ($line_num + 1));
                }

                continue;
            }

            /**
             * Выполнение PHP кода
             */
            elseif ($words[0][0] == '%')
            {
                $code = substr ($line, strlen ($words[0]));

                /**
                 * Обработка многострочного кода
                 */
                if (isset ($words[0][1]))
                {
                    if ($words[0][1] == '^')
                    {
                        $parsed = self::parseSubtext ($lines, $line_num, $height);

                        $code   .= $parsed[0];
                        $skip_at = $parsed[1];
                    }

                    else throw new \Exception ('Unknown char founded after runtime execution definition at line '. ($line_num + 1));
                }

                $tree->push (new Node ([
                    'type'   => RUNTIME_EXECUTION,
                    'line'   => $line,
                    'words'  => $words,
                    'height' => $height,

                    'args' => [
                        'code' => $code
                    ]
                ]));
            }

            /**
             * Установка свойства
             */
            elseif (($pos = strpos ($line, ':')) !== false)
            {
                if ($objects->size () == 0)
                    throw new \Exception ('Trying to set property to unknown object at line '. ($line_num + 1));

                if (!isset ($words[1]))
                    throw new \Exception ('Trying to set void property value at line '. ($line_num + 1));

                $propertyName  = substr ($line, 0, $pos);
                $propertyValue = substr ($line, $pos + 1);

                /**
                 * Обработка многострочных свойств
                 */
                if ($line[$pos + 1] == '^')
                {
                    $parsed = self::parseSubtext ($lines, $line_num, $height);

                    $propertyValue = substr ($propertyValue, 1) . $parsed[0];
                    $skip_at       = $parsed[1];
                }

                $objects->current ()->push (new Node ([
                    'type'   => PROPERTY_SET,
                    'line'   => $line,
                    'words'  => $words,
                    'height' => $height,

                    'args' => [
                        'name'  => $propertyName,
                        'value' => $propertyValue
                    ]
                ]));
            }

            /**
             * Вызов метода
             */
            elseif (isset ($words[0][1]) && $words[0][0] == '-' && $words[0][1] == '>')
            {
                if ($objects->size () == 0)
                    throw new \Exception ('Trying to call method from unknown object at line '. ($line_num + 1));

                $methodArgs = [];

                if (($pos = strpos ($line, '(')) !== false)
                {
                    if (($end = strrpos ($line, ')', $pos)) === false)
                        throw new \Exception ('Incorrect method arguments syntax at line '. ($line_num + 1));

                    $methodArgs = substr ($line, $pos + 1, $end - $pos - 1);

                    $methodName = trim (substr ($line, 2, $pos - 2));
                    $methodArgs = self::filter ($methodArgs) ?
                        self::parseArguments ($methodArgs) : [];
                }

                else $methodName = trim (substr ($line, 2));

                $objects->current ()->push (new Node ([
                    'type'   => METHOD_CALL,
                    'line'   => $line,
                    'words'  => $words,
                    'height' => $height,

                    'args' => [
                        'name' => $methodName,
                        'args' => $methodArgs
                    ]
                ]));
            }

            /**
             * Объявление объекта
             */
            elseif (sizeof ($words) > 1)
            {
                $class  = $words[0];
                $name   = $words[1];
                $args   = [];
                $styles = [];

                if ($objects->size () > 0 && $objects->current ()->height < $height)
                    $args[] = $objects->current ()->args['name'];

                if (($pos = strpos ($line, '(')) !== false)
                {
                    if (($end = strrpos ($line, ')', $pos)) === false)
                        throw new \Exception ('Incorrect class constructor arguments syntax at line '. ($line_num + 1));

                    $args = substr ($line, $pos + 1, $end - $pos - 1);

                    $name = substr ($line, $len = strlen ($class), $pos - $len);
                    $args = self::filter ($args) ?
                        self::parseArguments ($args) : [];
                }

                if (($end = strrpos ($line, ' > ')) !== false)
                {
                    $styles = trim (substr ($line, $end + 3));

                    if (strlen ($styles) == 0)
                        throw new \Exception ('Trying to set empty style to object');

                    $styles = array_map ('trim', explode (',', $styles));
                }

                $objects->push (new Node ([
                    'type'   => OBJECT_DEFINITION,
                    'line'   => $line,
                    'words'  => $words,
                    'height' => $height,

                    'args' => [
                        'class'  => $class,
                        'name'   => trim ($name),
                        'args'   => $args,
                        'styles' => $styles
                    ]
                ]));

                $tree->push ($objects->current ());
            }

            /**
             * Неопознанная структура
             */
            else throw new \Exception ('Unsupported structure founded at line '. ($line_num + 1));
        }

        return $tree;
    }

    /**
     * Подсчёт высоты строки (кол-во пробельных символов в её начале)
     * 
     * @param string &$line - строка
     * 
     * @return int - возвращает её высоту
     */
    protected static function getHeight (string &$line): int
    {
        $i = 0;
        $height = 0;

        while (isset ($line[$i]) && ctype_space ($line[$i]))
        {
            ++$height;

            if ($line[$i] == "\t")
                $height += 3;

            ++$i;
        }

        $line = substr ($line, $i);

        return $height;
    }

    /**
     * Проверка строки на пустоту
     * 
     * @param string $line - строка для проверки
     * 
     * @return bool - возвращает true если строка не пустая
     */
    protected static function filter (string $line): bool
    {
        return strlen (trim ($line)) > 0;
    }

    /**
     * Парсинг текста, лежащего на указанной высоте
     * 
     * @param array $lines     - массив строк
     * @param mixed $begin_id  - индекс начальной строки
     * @param int $down_height - минимальная высота строки
     * 
     * @return array - возвращает массив [текст, конечный индекс]
     */
    protected static function parseSubtext (array $lines, $begin_id, int $down_height): array
    {
        $parsed = "\n";

        foreach ($lines as $line_id => $line)
        {
            if ($line_id <= $begin_id)
                continue;

            if (!self::filter ($line))
            {
                $parsed .= "\n";
            
                continue;
            }

            $height = self::getHeight ($line);

            if ($height > $down_height)
                $parsed .= str_repeat (' ', $height - $down_height) ."$line\n";

            else return [$parsed, $line_id];
        }

        return [$parsed, $line_id + 1];
    }

    /**
     * Парсинг аргументов из текста
     * 
     * @param string $arguments - текст для парсинга
     * 
     * @return array - возвращает массив аргументов
     */
    protected static function parseArguments (string $arguments): array
    {
        $args = [];

        $split1   = $split2 = false;
        $canSplit = -1;

        $t = '';

        for ($i = 0, $len = strlen ($arguments); $i < $len; ++$i)
        {
            $t .= $arguments[$i];
            
            if ($arguments[$i] == '\\')
                $canSplit = $i + 1;

            elseif ($canSplit < $i)
            {
                if ($arguments[$i] == '\'' && !$split2)
                    $split1 = !$split1;

                elseif ($arguments[$i] == '"' && !$split1)
                    $split2 = !$split2;

                elseif (!$split1 && !$split2 && $arguments[$i] == ',')
                {
                    $args[] = substr ($t, 0, -1);
                    $t = '';
                }
            }
        }

        $args[] = $t;

        return $args;
    }
}
