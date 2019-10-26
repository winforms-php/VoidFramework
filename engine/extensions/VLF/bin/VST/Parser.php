<?php

namespace VLF\VST;

use \VLF\{
    AST,
    Stack,
    Node
};

/**
 * Парсер VST разметки
 */
class Parser extends \VLF\Parser
{
    /**
     * Парсер AST дерева из VST разметки
     * 
     * @param string $vst - VST разметка
     * 
     * @return AST - возвращает AST дерево разметки
     */
    public static function parse (string $vst): AST
    {
        $tree    = new AST;
        $objects = new Stack;

        if (file_exists ($vst))
            $vst = file_get_contents ($vst);

        $lines   = explode (self::$divider, $vst);
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
             * Комментарии
             */
            if ($words[0][0] == '#')
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
             * Создание нового стиля
             */
            elseif ($words[0][0] == '.')
            {
                $pos     = strpos ($line, ':');
                $parents = null;

                if ($pos !== false)
                {
                    $name = trim (substr ($line, 1, $pos - 1));

                    if (isset ($line[$pos]))
                    {
                        $parents = trim (substr ($line, $pos + 1));

                        if (strlen ($parents) == 0)
                            $parents = null;

                        else $parents = array_map ('trim', explode (',', $parents));
                    }
                }

                else $name = trim (substr ($line, 1));

                if ($parents === null && $objects->size () > 0 && $objects->current ()->height < $height)
                    $parents = [$objects->current ()->args['name']];

                $objects->push (new Node ([
                    'type'   => \VLF\STYLE_DEFINITION,
                    'line'   => $line,
                    'words'  => $words,
                    'height' => $height,

                    'args' => [
                        'name'    => $name,
                        'parents' => $parents
                    ]
                ]));

                $tree->push ($objects->current ());
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
                    'type'   => \VLF\PROPERTY_SET,
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
                    'type'   => \VLF\METHOD_CALL,
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
             * Неопознанная структура
             */
            else throw new \Exception ('Unsupported structure founded at line '. ($line_num + 1));
        }

        return $tree;
    }
}
