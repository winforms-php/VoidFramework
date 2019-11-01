<?php

namespace VLF\VST;

use \VLF\{
    AST,
    Node
};

/**
 * Интерпретатор AST VST разметки
 */
class Interpreter extends \VLF\Interpreter
{
    static array $styles = []; // Массив созданных стилей (название => стиль)
    static array $default_styles = []; // Массив дефолтных стилей

    static bool $throw_errors = true; // Выводить ли ошибки интерпретации
    static bool $allow_multimethods_calls = true; // Можно ли использовать многоуровневые вызовы методов (->method1->method2)

    /**
     * * Интерпретирование синтаксического дерева
     * Выполняет то, что было сгенерировано парсером VST кода
     * 
     * @param AST $tree - Абстрактное Синтаксическое Дерево (АСД), сгенерированное VST Parser'ом
     * [@param array $parent = null] - нода-родитель дерева (системная настройка)
     * 
     * @return array - возвращает список созданных стилей
     */
    public static function run (AST $tree, Node $parent = null): array
    {
        foreach ($tree->getNodes () as $id => $node)
        {
            if ($node->type == \VLF\STYLE_DEFINITION)
            {
                $name  = $node->args['name'];
                $nodes = $node->getNodes ();

                if ($node->args['parents'] !== null)
                    foreach ($node->args['parents'] as $parent)
                        if (!isset (self::$styles[$parent]) && !isset (self::$default_styles[$parent]))
                        {
                            if (self::$throw_errors)
                                throw new \Exception ('Style "'. $parent .'" not founded');
                        }

                        else $nodes = array_merge (self::$styles[$parent] ?? self::$default_styles[$parent], $nodes);

                if ($node->args['is_default'])
                    self::$default_styles[$name] = isset (self::$objects[$name]) ?
                        array_merge (self::$default_styles[$name], $nodes) : $nodes;

                else self::$styles[$name] = isset (self::$objects[$name]) ?
                    array_merge (self::$styles[$name], $nodes) : $nodes;
            }

            list (self::$styles, self::$default_styles) = self::run (new AST (array_map (
                fn ($node) => $node->export (), $node->getNodes ())), $node);
        }

        return [self::$styles, self::$default_styles];
    }
	
	public static function clear (): void
	{
		self::$styles = [];
		self::$default_styles = [];
	}
}
