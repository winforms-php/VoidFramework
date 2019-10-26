<?php

namespace VLF;

/**
 * AST дерево
 */
class AST
{
    // Список корневых нод
    protected array $nodes = [];

    /**
     * Конструктор дерева
     * 
     * [@param array $tree = null] - массив для импорта дерева
     */
    public function __construct (array $tree = null)
    {
        if ($tree !== null)
            $this->import ($tree);
    }

    /**
     * Импорт дерева
     * 
     * @param array $tree - массив для импорта
     * 
     * @return AST - возвращает сам себя
     */
    public function import (array $tree): AST
    {
        foreach ($tree as $node)
            $this->nodes[] = new Node ($node);

        return $this;
    }

    /**
     * Экспорт дерева
     * 
     * @return array - возращает массив экспортированного дерева
     */
    public function export (): array
    {
        return array_map (fn (Node $node) => $node->export (), $this->nodes);
    }

    /**
     * Добавить корневую ноду
     * 
     * @param Node $node - нода для добавления
     * 
     * @return AST - возвращает сам себя
     */
    public function push (Node $node): AST
    {
        $this->nodes[] = $node;

        return $this;
    }

    /**
     * Получить список корневых нод
     * 
     * @return array - возвращает список корневых нод
     */
    public function getNodes (): array
    {
        return $this->nodes;
    }
}
