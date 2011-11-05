<?php

/**
 * This source file is a part of the Knife CLI Tool for Fork CMS.
 * More information can be found on http://www.fork-cms.com
 *
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */
class KnifeAjaxGenerator extends KnifeBaseGenerator
{
	/**
	 * actionname
	 *
	 * @var	string
	 */
	protected $actionName, $fileName, $templateName, $inputName, $failArray, $successArray, $addBlock;

	/**
	 * Execute the action
	 */
	protected function init()
	{
		// get the action location
		foreach($this->arg as $key => $data)
		{
			// don't use the first key
			if($key == 0) continue;

			// build the data
			$actionData = $this->buildAjaxData($data);

			// build the action
			$this->buildLocationAction($this->arg[0], $actionData['location'], $actionData['actions']);

			// print the good actions
			if(empty($this->failArray)) $this->successHandler('The ' . $this->getLocation() . ' ajax handlers are created.');
			else $this->errorHandler(__CLASS__, 'buildAjax');
		}
	}

	/**
	 * Build the action info
	 *
	 * @return	array
	 * @param	string $data		The data to convert into an array.
	 */
	private function buildAjaxData($data)
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
	 * This will generate an action. It willn ot overwrite an existing action.
	 *
	 * The data needed for this action: 'modulename', 'location', 'actionname(s)'
	 *
	 * Examples:
	 * ft action blog backend edit,add,categories
	 * ft action blog frontend detail,archive
	 * ft action blog backend edit,delete,add_category frontend detail,archive
	 */
	protected function buildAjax()
	{
		// the location path
		$locationPath = ($this->getLocation() == 'frontend') ? FRONTENDPATH : BACKENDPATH;

		$actionFolder = $locationPath . 'modules/' . $this->getModuleFolder() . '/ajax';

		if(!is_dir($actionFolder)) mkdir($actionFolder);

		// action path
		$actionPath = $actionFolder . '/' . $this->fileName;

		// check if the action doesn't exist yet
		if(file_exists($actionPath)) throw new Exception('The ajax handler(' . $this->getLocation() . '/' .  strtolower($this->getModule()) . '/' . strtolower($this->actionName) . ') already exists.');

		// base file
		$baseFile = CLIPATH . 'knife/ajax/base/' . $this->getLocation() . '/base';

		// the action file
		$actionFile = $this->replaceFileInfo($baseFile . '.php');
		$this->makeFile($actionPath, $actionFile);

		// return
		return true;
	}

	/**
	 * Builds a specific location ajax handler
	 *
	 * @param	string $module		The module.
	 * @param	string $location	The working location.
	 * @param	string $actions		The actions to build
	 */
	private function buildLocationAction($module, $location, $actions)
	{
		// set variables
		$this->setLocation($location);
		$this->setModule($module);

		// arrays with succes and failures
		$this->successArray = array();
		$failArray = array();

		// seperate the actions
		$actionNames = str_replace(' ', '', $actions);
		$actionNames = explode(',', $actionNames);

		// loop the actions
		foreach($actionNames as $action)
		{
			// if we're working in the frontend, we might add a block
			if($this->getLocation() == 'frontend')
			{
				// explode for blocks
				$arrBlock = explode(':block', $action);

				// we need to create a block
				if(count($arrBlock) == 2)
				{
					$this->addBlock = true;
					$action = $arrBlock[0];
				}
			}

			// build action variables
			$this->inputName = $action;
			$this->actionName = $this->buildName($action);
			$this->fileName = $this->buildFileName($action);
			$this->templateName = $this->buildFileName($action, 'tpl');

			// build the action
			$success = $this->buildAjax();
			if($success) array_push($this->successArray, $this->actionName);
			else array_push($this->failArray, $this->actionName);
		}
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
		$fileInput = str_replace('actionname', $this->actionName, $fileInput);
		$fileInput = str_replace('versionname', VERSION, $fileInput);
		$fileInput = str_replace('authorname', AUTHOR, $fileInput);

		// return
		return $fileInput;
	}
}
