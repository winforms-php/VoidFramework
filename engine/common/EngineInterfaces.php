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
			
			case 'name':
				try
				{
					return $this->getProperty ('Name');
				}
				
				catch (\WinformsException $e)
				{
					return $this->name;
				}
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
			
		elseif (strtolower ($name) == 'name')
		{
			try
			{
				$this->setProperty ($name, $value);
			}
			
			catch (\WinformsException $e)
			{
				$this->name = $value;
			}
		}
        
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
