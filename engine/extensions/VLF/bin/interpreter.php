<?php

namespace VLF;

/**
 * Интерпретатор AST VLF разметки
 */
class Interpreter
{
    static array $objects = []; // Массив созданных объектов (название => объект)

    static bool $throw_errors = true; // Выводить ли ошибки интерпретации
    static bool $allow_multimethods_calls = true; // Можно ли использовать многоуровневые вызовы методов (->method1->method2)

    /**
     * Интерпретирование синтаксического дерева
     * 
     * @param AST $tree - Абстрактное Синтаксическое Дерево (АСД), сгенерированное VLF Parser'ом
     * [@param array $parent = null] - нода-родитель дерева (системная настройка)
     * 
     * @return array - возвращает список созданных объектов
     */
    public static function run (AST $tree, Node $parent = null): array
    {
        foreach ($tree->getNodes () as $id => $node)
        {
            switch ($node->type)
            {
                case OBJECT_DEFINITION:
                    $class = $node->args['class'];
                    $name  = $node->args['name'];
                    $args  = [];

                    if (isset (self::$objects[$name]))
                        break;

                    if (isset ($node->args['args']))
                    {
                        $args = $node->args['args'];

                        foreach ($args as $arg_id => $arg)
                            $args[$arg_id] = self::formatLine ($arg, self::$objects);
                    }

                    try
                    {
                        self::$objects[$name] = eval ("namespace VoidEngine; return new $class (". implode (', ', $args) .");");

                        try
                        {
                            self::$objects[$name]->name = $name;
                        }

                        catch (\Throwable $e) {}
                    }

                    catch (\Throwable $e)
                    {
                        if (self::$throw_errors)
                            throw new \Exception ('Interpeter couldn\'t create object "'. $class .'" with name "'. $name .'" at line "'. $node->line .'". Exception info:'. "\n\n". (string) $e, 0, $e);
                    }
                break;

                case PROPERTY_SET:
                    if ($parent !== null)
                    {
                        $name = $parent->args['name'];

                        $propertyName  = $node->args['name'];
                        $propertyValue = $node->args['value'];
                        $preset        = '';

                        if (preg_match ('/function \((.*)\) use \((.*)\)/', $propertyValue))
                        {
                            $use = substr ($propertyValue, strpos ($propertyValue, 'use'));
                            $use = $ouse = substr ($use, ($pos = strpos ($use, '(') + 1), strpos ($use, ')') - $pos);
                            $use = explode (' ', $use);

                            foreach ($use as $id => $useParam)  
                                if (isset (self::$objects[$useParam]) && $use[$id + 1][0] == '$')
                                {
                                    $fname = $use[$id + 1];

                                    if (substr ($fname, strlen ($fname) - 1) == ',')
                                        $fname = substr ($fname, 0, -1);

                                    $preset .= "$fname = $useParam; ";

                                    unset ($use[$id]);
                                }

                            $preset        = self::formatLine ($preset, self::$objects);
                            $propertyValue = self::formatLine (str_replace ($ouse, implode (' ', $use), $propertyValue), self::$objects);
                        }

                        else $propertyValue = self::formatLine ($propertyValue, self::$objects);

                        try
                        {
							if (strpos ($propertyName, '->') !== false && self::$allow_multimethods_calls)
                                eval ('namespace VoidEngine; '. $preset .' _c('. self::$objects[$name]->selector .')->'. $propertyName .' = '. $propertyValue .';');
                            
                            else self::$objects[$name]->$propertyName = eval ("namespace VoidEngine; $preset return $propertyValue;");
                        }

                        catch (\Throwable $e)
                        {
                            if (self::$throw_errors)
                                throw new \Exception ('Interpeter couldn\'t set property "'. $propertyName .'" with value "'. $propertyValue .'" at line "'. $node->line .'". Exception info:'. "\n\n". (string) $e, 0, $e);
                        }
                    }

                    elseif (self::$throw_errors)
                        throw new \Exception ('Setting property to an non-object at line "'. $node->line);
                break;

                case METHOD_CALL:
                    if ($parent !== null)
                    {
                        $name = $parent->args['name'];

                        $methodName = $node->args['name'];
                        $methodArgs = $node->args['args'];

                        foreach ($methodArgs as $arg_id => $arg)
                            $methodArgs[$arg_id] = self::formatLine ($arg, self::$objects);

                        try
                        {
                            if (strpos ($methodName, '->') !== false && self::$allow_multimethods_calls)
                                eval ('namespace VoidEngine; _c('. self::$objects[$name]->selector .')->'. $methodName .' ('. implode (', ', $methodArgs) .');');

                            elseif (sizeof ($methodArgs) > 0)
                                self::$objects[$name]->$methodName (...eval ('namespace VoidEngine; return ['. implode (', ', $methodArgs) .'];'));

                            else self::$objects[$name]->$methodName ();
                        }

                        catch (\Throwable $e)
                        {
                            if (self::$throw_errors)
                                throw new \Exception ('Interpeter couldn\'t call method "'. $methodName .'" with arguments '. json_encode ($methodArgs) .' at line "'. $node->line .'". Exception info:'. "\n\n". (string) $e, 0, $e);
                        }
                    }

                    elseif (self::$throw_errors)
                        throw new \Exception ('Calling method to an non-object at line "'. $node->line .'"');
                break;

                case STYLES_IMPORTING:
                    foreach ($node->args['imports'] as $style)
                    {
                        $path = eval ('namespace VoidEngine; return '. self::formatLine ($style, self::$objects) .';');

                        if (!file_exists ($path))
                            throw new \Exception ('Trying to import nonexistent style at line "'. $node->line .'"');
                        
                        \VLF\VST\Interpreter::run (\VLF\VST\Parser::parse (file_get_contents ($path)));
                    }
                break;

                case RUNTIME_EXECUTION:
                    eval (self::formatLine ($node->args['code'], self::$objects));
                break;
            }

            $nodes = $node->getNodes ();

            if (isset ($node->args['styles']))
                foreach ($node->args['styles'] as $style)
                    if (isset (\VLF\VST\Interpreter::$styles[$style]))
                        $nodes = array_merge ($nodes, \VLF\VST\Interpreter::$styles[$style]);

                    else throw new \Exception ('Trying to set undefined style to object at line "'. $node->line .'"');

            self::$objects = self::run (new AST (array_map (
                fn ($node) => $node->export (), $nodes)), $node);
        }

        return self::$objects;
    }

    /**
     * Форматирование строки
     * 
     * @param string $line - строка для форматирования
     * [@param array $objects = []] - список объектов, которые будут участвовать в форматировании
     * 
     * @return string - возвращает форматированную строку
     */
    public static function formatLine (string $line, array $objects = []): string
    {
        if (sizeof ($objects) > 0)
        {
            $len     = strlen ($line);
            $newLine = '';

            $replacement = array_map (function ($object)
            {
                return \VoidEngine\Components::exists ($object->selector) !== false ? 
                    '\VoidEngine\_c('. $object->selector .')' :
                    'unserialize (\''. serialize ($object) .'\')';
            }, $objects);

            $replacement = array_map (function ($name)
            {
                return strlen ($name = trim ($name)) + substr_count ($name, '_');
            }, $omap = array_flip ($replacement));

            arsort ($replacement);

            $nReplacement = [];

            foreach ($replacement as $replaceTo => $nLn)
                $nReplacement[$omap[$replaceTo]] = $replaceTo;

            $replacement = $nReplacement;
            $blacklist   = array_flip (['\'', '"', '$']);

            for ($i = 0; $i < $len; ++$i)
            {
                $replaced = false;

                foreach ($replacement as $name => $replaceAt)
                    if (substr ($line, $i, ($l = strlen ($name))) == $name && !isset ($blacklist[$line[$i - 1]]))
                    {
                        $newLine .= $replaceAt;

                        $i += $l - 1;
                        $replaced = true;

                        break;
                    }

                if (!$replaced)
                    $newLine .= $line[$i];
            }

            $line = $newLine;
        }

        return $line;
    }
}
