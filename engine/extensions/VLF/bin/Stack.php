<?php

namespace VLF;

/**
 * Структура данных стэк
 */
class Stack
{
    // Массив элементов
    protected array $items = [];

    /**
     * Добавить элемент в стэк
     * 
     * @param mixed $item - элемент
     * 
     * @return Stack - возвращает сам себя
     */
    public function push ($item): Stack
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Вывести текущий элемент стэка
     * 
     * @return mixed - возвращает текущий элемент
     */
    public function current ()
    {
        return end ($this->items);
    }

    /**
     * Вывести текущий элемент стэка и удалить его
     * 
     * @return mixed - возвращает текущий элемент
     */
    public function pop ()
    {
        return array_pop ($this->items);
    }

    /**
     * Вывести размер стэка
     * 
     * @return int - возвращает размер стэка
     */
    public function size (): int
    {
        return sizeof ($this->items);
    }
}
