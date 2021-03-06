<?php

namespace VoidEngine;

use VoidCore;

class NetObject implements \ArrayAccess
{
    protected int $selector = 0;
    protected ?string $name = null;
    // protected bool $isCollection = false;

    public function __construct ($name, $assembly = false, ...$args)
    {
        foreach ($args as $id => $arg)
            $args[$id] = EngineAdditions::uncoupleSelector ($arg);
        
        if (is_int ($name) && VoidCore::objectExists ($name))
            $this->selector = $name;

        elseif (is_string ($name))
            $this->selector = VoidCore::createObject ($name, $assembly, ...$args);

        else throw new \Exception ('Incorrect params passed');

        /*$this->isCollection = $this->getType ()
            ->isSubclassOf (VoidCore::typeof ('System.Collectons.Generic.ICollection'));*/
    }

    public function dispose (): void
    {
        VoidCore::removeObjects ($this->selector);
    }

    # Основные магические методы

    public function __get (string $name)
    {
        switch (strtolower ($name))
        {
            case 'count':
            case 'length':
                try
                {
                    return $this->getProperty ('Count');
                }

                catch (\WinformsException $e)
                {
                    return $this->getProperty ('Length');
                }
            break;

            case 'list':
                $size = $this->count;
                $list = [];
                
				for ($i = 0; $i < $size; ++$i)
                    $list[] = EngineAdditions::coupleSelector (VoidCore::getArrayValue ($this->selector, $i));
                
                return $list;
            break;

            case 'names':
                $size  = $this->count;
                $names = [];
                
                for ($i = 0; $i < $size; ++$i)
                    try
                    {
                        $names[] = VoidCore::getProperty (VoidCore::getArrayValue ($this->selector, [$i, VC_OBJECT]), 'Text');
                    }

                    catch (\WinformsException $e)
                    {
                        $names[] = VoidCore::getArrayValue ($this->selector, [$i, VC_STRING]);
                    }
                
                return $names;
            break;
        }

        if (method_exists ($this, $method = 'get_'. $name))
            return $this->$method ();

        return isset ($this->$name) ?
            $this->$name : EngineAdditions::coupleSelector ($this->getProperty ($name));
    }

    public function __set (string $name, $value): void
    {
        if (substr ($name, -5) == 'Event')
            Events::setEvent ($this->selector, substr ($name, 0, -5), $value);

        elseif (method_exists ($this, $method = 'set_'. $name))
            $this->$method ($value);
        
        else $this->setProperty ($name, EngineAdditions::uncoupleSelector ($value));
    }

    public function __call (string $name, array $args)
    {
        return EngineAdditions::coupleSelector ($this->callMethod ($name,
            array_map ('VoidEngine\\EngineAdditions::uncoupleSelector', $args)));
    }

    # Управление VoidCore

    protected function getProperty ($name)
    {
        return VoidCore::getProperty ($this->selector, $name);
    }

    protected function setProperty (string $name, $value): void
    {
        VoidCore::setProperty ($this->selector, $name, $value);
    }

    protected function callMethod (string $name, array $args = [])
    {
        return VoidCore::callMethod ($this->selector, $name, ...$args);
    }

    # ArrayAccess

    public function offsetSet ($index, $value)
	{
        try
        {
            $index === null ?
                $this->callMethod ('Add', EngineAdditions::uncoupleSelector ($value)) :
                $this->callMethod ('Insert', $index, EngineAdditions::uncoupleSelector ($value));
        }

        catch (\Throwable $e)
        {
            $index === null ?
                VoidCore::setArrayValue ($this->selector, $this->count, EngineAdditions::uncoupleSelector ($value)) :
                VoidCore::setArrayValue ($this->selector, $index, EngineAdditions::uncoupleSelector ($value));
        }
    }
	
	public function offsetGet ($index)
	{
		return EngineAdditions::coupleSelector (VoidCore::getArrayValue ($this->selector, $index), $this->selector);
    }
	
	public function offsetUnset ($index): void
	{
		$this->callMethod ('RemoveAt', $index);
    }
    
    public function offsetExists ($index): bool
    {
        try
        {
            $this->offsetGet ($index);
        }

        catch (\WinformsException $e)
        {
            return false;
        }

        return true;
    }

    # Итерация массивов

    public function foreach (callable $callback, string $type = null): void
    {
        $size = $this->count;

        for ($i = 0; $i < $size; ++$i)
            $callback (EngineAdditions::coupleSelector (VoidCore::getArrayValue ($this->selector, $type !== null ? [$i, $type] : $i), $this->selector), $i);
    }

    public function where (callable $comparator, string $type = null): array
    {
        $size   = $this->count;
        $return = [];

        for ($i = 0; $i < $size; ++$i)
            if ($comparator ($value = EngineAdditions::coupleSelector (VoidCore::getArrayValue ($this->selector, $type !== null ? [$i, $type] : $i), $this->selector), $i))
                $return[] = $value;

        return $return;
    }

    # Магические методы

    public function __destruct ()
    {
        VoidCore::destructObject ($this->selector);
    }

    public function __toString (): string
    {
        return $this->selector;
    }

    public function __debugInfo (): array
    {
        $info = ['selector' => $this->selector];

        try
        {
            $info['name'] = $this->getProperty ('Name');
        }

        catch (\WinformsException $e) {}

        try
        {
            $info['info'] = $this->callMethod ('ToString');
        }

        catch (\WinformsException $e) {}

        return $info;
    }
}

class NetClass extends NetObject
{
    public function __construct ($name, $assembly = false)
    {
        if (is_int ($name) && VoidCore::objectExists ($name))
            $this->selector = $name;

        elseif (is_string ($name))
            $this->selector = VoidCore::getClass ($name, $assembly);

        else throw new \Exception ('Incorrect params passed');
    }
}

class EngineAdditions
{
	/**
     * * Компиляция PHP кода
     * 
     * TODO: дополнить описание
     * 
     * @param string $savePath - путь для компиляции
     * @param string $iconPath - путь до иконки
     * @param string $phpCode - код для компиляции без тэгов
     * 
     * [@param string $productDescription = null] - описание приложения
     * [@param string $productName = null]        - название приложения
     * [@param string $productVersion = null]     - версия приложения
     * [@param string $companyName = null]        - компания-производителя
     * [@param string $copyright = null]          - копирайт
     * [@param string $callSharpCode = '']        - чистый C# код
     * [@param string $declareSharpCode = '']     - C# код с объявлениями классов
     * 
     * @return array - возвращает список ошибок компиляции
     */
    public static function compile (string $savePath, string $iconPath, string $phpCode, string $productDescription = null, string $productName = null, string $productVersion = null, string $companyName = null, string $copyright = null, string $callSharpCode = '', string $declareSharpCode = '', NetObject $dictionary = null, NetObject $assemblies = null): array
    {
        if ($dictionary === null)
            $dictionary = new NetObject ('System.Collections.Generic.Dictionary`2[System.String,System.String]', null);

        if ($assemblies === null)
            $assemblies = dnArray ('System.String', []);

        if ($productName === null)
            $productName = basenameNoExt ($savePath);

        if ($productDescription === null)
            $productDescription = $productName;

        if ($productVersion === null)
            $productVersion = '1.0';

        if ($companyName === null)
            $companyName = 'Company N';

        if ($copyright === null)
            $copyright = $companyName .' copyright (c) '. date ('Y');

        return (new NetClass ('WinForms_PHP.WFCompiler', null))->compile ($savePath, $iconPath, $phpCode, $productDescription, $productName, $productVersion, $companyName, $copyright, $callSharpCode, $declareSharpCode, $dictionary, $assemblies)->names;
    }

    public static function loadModule (string $path): bool
    {
        try
        {
            (new NetClass ('System.Reflection.Assembly', 'mscorlib'))->loadFrom ($path);
        }

        catch (\WinformsException $e)
        {
            return false;
        }

        return true;
    }
	
    public static function coupleSelector ($selector)
    {
        return is_int ($selector) && VoidCore::objectExists ($selector) ?
            new NetObject ($selector) : $selector;
    }

    public static function uncoupleSelector ($object)
    {
        return is_object ($object) && $object instanceof NetObject ?
            $object->selector : $object;
    }
}
