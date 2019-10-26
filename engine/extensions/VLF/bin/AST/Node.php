<?php

namespace VLF;

/**
 * Нода синтаксического дерева
 */
class Node
{
    public ?string $line = null; // Строка
    public array $words  = [];   // Список слов
    public int $height   = 0;    // Высота строки
    public int $type     = 0;    // Тип ноды
    public array $args   = [];   // Аргументы ноды

    protected array $nodes = []; // Ветви ноды

    /**
     * Конструктор ноды
     * 
     * [@param array $node = null] - массив для импорта ноды
     */
    public function __construct (array $node = null)
    {
        if ($node !== null)
            $this->import ($node);
    }

    /**
     * Импорт ноды
     * 
     * @param array $node - массив для импорта
     * 
     * @return Node - возвращает саму себя
     */
    public function import (array $node): Node
    {
        $this->line   = $node['line'] ?? null;
        $this->words  = $node['words'] ?? [];
        $this->height = $node['height'] ?? 0;
        $this->type   = $node['type'] ?? 0;
        $this->args   = $node['args'] ?? [];

        $this->nodes = array_map (fn (array $t) => new Node ($t), $node['nodes'] ?? []);

        return $this;
    }

    /**
     * Экспорт ноды
     * 
     * @return array - возвращает массив экспортированной ноды
     */
    public function export (): array
    {
        return [
            'line'   => $this->line,
            'words'  => $this->words,
            'height' => $this->height,
            'type'   => $this->type,
            'args'   => $this->args,

            'nodes'  => array_map (fn (Node $node) => $node->export (), $this->nodes)
        ];
    }

    /**
     * Добавить ветвь ноды
     * 
     * @param Node $node - нода для добавления
     * 
     * @return Node - возвращает саму себя
     */
    public function push (Node $node): Node
    {
        $this->nodes[] = $node;

        return $this;
    }

    /**
     * Получить список ветвей ноды
     * 
     * @return array - возвращает список ветвей
     */
    public function getNodes (): array
    {
        return $this->nodes;
    }
}
