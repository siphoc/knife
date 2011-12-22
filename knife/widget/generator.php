<?php

/**
 * This source file is a part of the Knife CLI Tool for Fork CMS.
 * More information can be found on http://www.fork-cms.com
 *
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
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
		// get the action location
		foreach($this->arg as $key => $data)
		{
			// don't use the first key
			if($key == 0) continue;

			// build the data
			$widgetData = $this->buildWidgetData($data);

			// build the action
			$this->buildLocationWidget($this->arg[0], $widgetData['location'], $widgetData['actions']);

			// print the good widgets
			$this->successHandler('The widgets are created.');
			if(!empty($failArray)) $this->errorHandler(__CLASS__, 'buildWidget');
		}
	}

	/**
	 * This will generate an widget. It willn ot overwrite an existing widget.
	 *
	 * The data needed for this widget: 'modulename', 'location', 'widgetname(s)'
	 *
	 * Examples:
	 * ft widget blog b=edit,add,categories
	 * ft widget blog f=detail,archive
	 * ft widget blog b=edit,delete,add_category f=detail,archive
	 */
	protected function buildWidget()
	{
		// the location path
		$locationPath = ($this->getLocation() == 'frontend') ? FRONTENDPATH : BACKENDPATH;

		$actionFolder = $locationPath . 'modules/' . $this->getModuleFolder() . '/widgets';
		$templateFolder = $locationPath . 'modules/' . $this->getModuleFolder() . '/layout/widgets/';

		if(!is_dir($actionFolder)) mkdir($actionFolder);
		if(!is_dir($templateFolder)) mkdir($templateFolder);

		// widget path
		$widgetPath = $actionFolder . '/' . $this->fileName;
		$templatePath = $templateFolder . '/' . $this->templateName;

		// check if the widget doesn't exist yet
		if(file_exists($widgetPath)) throw new Exception('The widget(' . $this->getLocation() . '/' .  $this->getModuleFolder() . '/' . strtolower($this->widgetName) . ') already exists.');

		// backend widget
		$baseWidget = 'base';

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
	 * Build the action info
	 *
	 * @return	array
	 * @param	string $data		The data to convert into an array.
	 */
	private function buildWidgetData($data)
	{
		// the data array
		$arrReturn = array();
		$arrData = explode('=', $data);

		// is this a valid location
		if($arrData[0] != 'f' && $arrData[0] != 'frontend' && $arrData[0] != 'backend' && $arrData[0] != 'b')
		{
			throw new Exception('This(' . $arrData[0] . ' is not a valid location');
		}

		// are there actions givne?
		if(!isset($arrData[1])) throw new Exception('You need to provide at least one action');

		// get the location
		$arrReturn['location'] = ($arrData[0] == 'f' || $arrData == 'frontend') ? 'frontend' : 'backend';

		// the actions
		$arrReturn['actions'] = $arrData[1];

		// return
		return $arrReturn;
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
			$dbTable = (VERSIONCODE < 3) ? 'pages_extras' : 'modules_extras';

			// set next sequence number for this module
			$sequence = Knife::getDB()->getVar(
				'SELECT MAX(sequence) + 1
				 FROM ' . $dbTable . '
				 WHERE module = ?',
				array((string) $this->getModuleFolder())
			);

			// set the parameters
			$parameters['module'] = $this->getModuleFolder();
			$parameters['type'] = 'widget';
			$parameters['label'] = $this->widgetName;
			$parameters['action'] = strtolower($this->buildFileName($this->inputName, ''));
			$parameters['sequence'] = $sequence;
			$db->insert($dbTable, $parameters);

			// read the installer into an array
			if(file_exists(BACKENDPATH . 'modules/' . $this->getModuleFolder() . '/installer/install.php'))
			{
				$installer = BACKENDPATH . 'modules/' . $this->getModuleFolder() . '/installer/install.php';
			}
			elseif(file_exists(BACKENDPATH . 'modules/' . $this->getModuleFolder() . '/installer/installer.php'))
			{
				$installer = BACKENDPATH . 'modules/' . $this->getModuleFolder() . '/installer/installer.php';
			}
			else throw new Exception('The installer is not found.');
			$aInstall = file($installer);

			// the new file array
			$fileArray = array();

			// fileKey
			$fileKey = 0;
			$insert = false;

			// loop the installer lines
			foreach($aInstall as $key => $line)
			{
				// trim the line
				$trimmedLine = trim($line);
				if($insert)
				{
					// the new rule
					$fileArray[$fileKey] = "\t\t" . '$this->insertExtra(\'' . $this->getModuleFolder() . '\', \'widget\', \'' . $parameters['label'] . '\', \'' . $parameters['action'] . '\');' . "\n";

					// reset the line key
					$fileKey++;
					$insert = false;
				}

				// get the index action, this should always be present
				if($trimmedLine == "// add extra's") $insert = true;

				// add the line
				$fileArray[$fileKey] = $line;

				// count up
				$fileKey++;
			}

			// rewrite the file
			file_put_contents($installer, $fileArray);
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
		$fileInput = str_replace('authorname', AUTHOR, $fileInput);

		// return
		return $fileInput;
	}
}
