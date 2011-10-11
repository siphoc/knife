<?php

/**
 * This source file is a part of the Knife CLI Tool for Fork CMS.
 * More information can be found on http://www.fork-cms.com
 *
 * @package		knife
 * @subpackage	widget
 *
 * @author		Jelmer Snoeck <jelmer.snoeck@netlash.com>
 * @since		0.5
 */
class KnifeWidgetGenerator extends KnifeBaseGenerator
{
	/**
	 * widgetname
	 *
	 * @var	string
	 */
	protected $widgetName, $fileName, $templateName, $inputName, $successArray;

	/**
	 * Execute the widget
	 */
	protected function init()
	{
		// build the first location widgets
		$this->buildLocationWidget($this->arg[0], @$this->arg[1], @$this->arg[2]);

		// is there a second location given?
		if(isset($this->arg[3])) $this->buildLocationWidget($this->arg[0], $this->arg[3], @$this->arg[4]);

		// print the good widgets
		$this->successHandler('The widgets ' . implode(', ', $this->successArray) . ' are created.');
		if(!empty($failArray)) $this->errorHandler(__CLASS__, 'buildWidget');
	}

	/**
	 * This will generate an widget. It willn ot overwrite an existing widget.
	 *
	 * The data needed for this widget: 'modulename', 'location', 'widgetname(s)'
	 *
	 * Examples:
	 * ft widget blog backend edit,add,categories
	 * ft widget blog frontend detail,archive
	 * ft widget blog backend edit,delete,add_category frontend detail,archive
	 */
	protected function buildWidget()
	{
		// widget path
		$widgetPath = BASEPATH . 'default_www/' . $this->getLocation() . '/modules/' . strtolower($this->getModule()) . '/widgets/' . $this->fileName;
		$templatePath = BASEPATH . 'default_www/' . $this->getLocation() . '/modules/' . strtolower($this->getModule()) . '/layout/widgets/' . $this->templateName;

		// check if the widget doesn't exist yet
		if(file_exists($widgetPath)) throw new Exception('The widget(' . $this->getLocation() . '/' .  strtolower($this->getModule()) . '/' . strtolower($this->widgetName) . ') already exists.');

		// backend widget
		if($this->getLocation() == 'backend')
		{

		}
		// frontend aciton
		else
		{
			$baseWidget = 'base';
		}

		// insert info in the database to grant access
		if(!$this->databaseInfo()) return false;

		// base file
		$baseFile = CLIPATH . 'knife/widget/base/' . $this->getLocation() . '/' . $baseWidget;

		// the widget file
		$widgetFile = $this->replaceFileInfo($baseFile . '.php');
		$this->makeFile($widgetPath, $widgetFile);

		// create template file
		$widgetTpl = $this->replaceFileInfo($baseFile . '.tpl');
		$this->makeFile($templatePath, $widgetTpl);

		return true;
	}

	/**
	 * Builds a specific location widget
	 *
	 * @param	string $module		The module.
	 * @param	string $location	The working location.
	 * @param	string $widgets		The widgets to build
	 */
	private function buildLocationWidget($module, $location, $widgets = null)
	{
		// do we have a second widget parameter?
		if(isset($location) && $widgets == null) throw new Exception('Please provide an widget name.');

		// set variables
		$this->setLocation($location);
		$this->setModule($module);

		// arrays with succes and failures
		$this->successArray = array();
		$failArray = array();

		// seperate the widgets
		$widgetNames = str_replace(' ', '', $widgets);
		$widgetNames = explode(',', $widgetNames);

		// loop the widgets
		foreach($widgetNames as $widget)
		{
			// build widget variables
			$this->inputName = $widget;
			$this->widgetName = $this->buildName($widget);
			$this->fileName = $this->buildFileName($widget);
			$this->templateName = $this->buildFileName($widget, 'tpl');

			// build the widget
			$success = $this->buildWidget();
			if($success) array_push($this->successArray, $this->widgetName);
			else array_push($failArray, $this->widgetName);
		}
	}

	/**
	 * Inserts a backend widget into the database.
	 */
	private function databaseInfo()
	{
		// database instance
		$db = Knife::getDB(true);

		try
		{
			// set the parameters
			$parameters['module'] = strtolower($this->getModule());
			$parameters['type'] = 'widget';
			$parameters['label'] = $this->widgetName;
			$parameters['action'] = strtolower($this->buildFileName($this->inputName, ''));

			// insert
			$db->insert('pages_extras', $parameters);
		}
		// houston, we have a problem.
		catch(Exception $e)
		{
			if(DEV_MODE) throw new Exception('Something went wrong while inserting the data into the database.');
			else return false;
		}

		// return
		return true;
	}

	/**
	 * Replaces the info in a file with the given parameters
	 *
	 * @return	string
	 * @param	string $file		The file name.
	 */
	private function replaceFileInfo($file)
	{
		// replace
		$fileInput = $this->readFile($file);
		$fileInput = str_replace('modulename', $this->getModule(), $fileInput);
		$fileInput = str_replace('subname', strtolower($this->getModule()), $fileInput);
		$fileInput = str_replace('widgetname', $this->widgetName, $fileInput);
		$fileInput = str_replace('versionname', VERSION, $fileInput);
		$fileInput = str_replace('authorname', AUTHOR, $fileInput);

		// return
		return $fileInput;
	}
}
