<?php

namespace VoidEngine;

use VoidCore;

abstract class Control extends Component
{
    public function __construct (NetObject $parent = null, ...$args)
    {
        parent::__construct (...$args);

        if ($parent !== null)
            $this->parent = $parent;
    }

    public function get_caption (): string
    {
        return $this->text;
    }
	
    public function set_caption (string $caption): void
    {
        $this->text = $caption;
    }
	
	public function set_font ($font): void
	{
        if (is_array ($font))
        {
            $font = array_values ($font);

            $obj = isset ($font[2]) ?
                VoidCore::createObject ('System.Drawing.Font', 'System.Drawing', $font[0], $font[1], [$font[2], 'System.Drawing.FontStyle, System.Drawing']) :
                VoidCore::createObject ('System.Drawing.Font', 'System.Drawing', $font[0], $font[1]);
            
            $this->setProperty ('Font', $obj);
        }

        else $this->setProperty ('Font', EngineAdditions::uncoupleSelector ($font));
    }
	
    public function get_backgroundColor ()
    {
        return $this->getProperty (['BackColor', VC_COLOR]);
    }
	
    public function set_backgroundColor ($color): void
    {
        $this->setProperty ('BackColor', $color);
    }
	
    public function get_foregroundColor ()
    {
        return $this->getProperty (['ForeColor', VC_COLOR]);
    }
	
    public function set_foregroundColor ($color): void
    {
        $this->setProperty ('ForeColor', $color);
    }
	
    public function get_w (): int
    {
        return $this->width;
    }
	
    public function set_w (int $w): void
    {
        $this->width = $w;
    }
	
    public function get_h (): int
    {
        return $this->height;
    }
	
    public function set_h (int $h): void
    {
        $this->height = $h;
    }
	
    public function get_x (): int
    {
        return $this->left;
    }
	
    public function set_x (int $x): void
    {
        $this->left = $x;
    }
	
    public function get_y (): int
    {
        return $this->top;
    }
	
    public function set_y (int $y): void
    {
        $this->top = $y;
    }

    public function get_bounds (): array
    {
        return [
            $this->left,
            $this->top,
            $this->width,
            $this->height
        ];
    }
	
    public function set_bounds ($bounds): void
    {
        if (is_array ($bounds))
        {
            $bounds = array_values ($bounds);
            
            $this->left   = (int) $bounds[0];
            $this->top    = (int) $bounds[1];
            $this->width  = (int) $bounds[2];
            $this->height = (int) $bounds[3];
        }

        else $this->setProperty ('Bounds', EngineAdditions::uncoupleSelector ($bounds));
    }
	
    public function get_location (): array
    {
        return [
            $this->left,
            $this->top
        ];
    }
	
    public function set_location ($location)
    {
        if (is_array ($location))
        {
            $location = array_values ($location);
            
            $this->left = $location[0];
            $this->top  = $location[1];
        }

        else $this->setProperty ('Location', EngineAdditions::uncoupleSelector ($location));
    }
	
    public function get_size (): array
    {
        return [
            $this->width,
            $this->height
        ];
    }
	
    public function set_size ($size): void
    {
        if (is_array ($size))
        {
            $size = array_values ($size);
            
            $this->width  = (int) $size[0];
            $this->height = (int) $size[1];
        }

        else $this->setProperty ('Size', EngineAdditions::uncoupleSelector ($size));
    }
	
    public function setBounds (int $x, int $y, int $w, int $h): void
    {
        $this->set_bounds ([$x, $y, $w, $h]);
    }
	
    public function setLocation (int $x, int $y): void
    {
        $this->set_location ([$x, $y]);
    }
	
    public function setSize (int $w, int $h): void
    {
        $this->set_size ([$w, $h]);
    }
	
    public function toBack (): void
    {
        $this->callMethod ('SendToBack');
    }
	
    public function toFront (): void
    {
        $this->callMethod ('BringToFront');
    }
}
