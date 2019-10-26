<?php

namespace VoidEngine;

class Chart extends Control
{
	protected ?string $classname = 'System.Windows.Forms.DataVisualization.Charting.Chart';
	protected ?string $assembly  = 'System.Windows.Forms.DataVisualization';
}

class Annotation extends Control
{
	protected ?string $classname = 'System.Windows.Forms.DataVisualization.Charting.Annotation';
	protected ?string $assembly  = 'System.Windows.Forms.DataVisualization';
}

class ChartArea extends Control
{
	protected ?string $classname = 'System.Windows.Forms.DataVisualization.Charting.ChartArea';
	protected ?string $assembly  = 'System.Windows.Forms.DataVisualization';
}

class Legend extends Control
{
	protected ?string $classname = 'System.Windows.Forms.DataVisualization.Charting.Legend';
	protected ?string $assembly  = 'System.Windows.Forms.DataVisualization';
}

class Series extends Control
{
	protected ?string $classname = 'System.Windows.Forms.DataVisualization.Charting.Series';
	protected ?string $assembly  = 'System.Windows.Forms.DataVisualization';
}

class Title extends Control
{
	protected ?string $classname = 'System.Windows.Forms.DataVisualization.Charting.Title';
	protected ?string $assembly  = 'System.Windows.Forms.DataVisualization';
}
