<?php

/**
 * This source file is a part of the Knife CLI Tool for Fork CMS.
 * More information can be found on http://www.fork-cms.com
 *
 * @package		knife
 * @subpackage	action
 *
 * @author		Jelmer Snoeck <jelmer.snoeck@netlash.com>
 * @since		0.3
 */
class KnifeActionGenerator extends KnifeBaseGenerator
{
	/**
	 * actionname
	 *
	 * @var	string
	 */
	protected $actionName, $fileName, $templateName, $inputName, $successArray;

	/**
	 * Execute the action
	 */
	protected function init()
	{
		// build the first location actions
		$this->buildLocationAction($this->arg[0], @$this->arg[1], @$this->arg[2]);

		// is there a second location given?
		if(isset($this->arg[3])) $this->buildLocationAction($this->arg[0], $this->arg[3], @$this->arg[4]);

		// print the good actions
		$this->successHandler('The actions ' . implode(', ', $this->successArray) . ' are created.');
		if(!empty($failArray)) $this->errorHandler(__CLASS__, 'buildAction');
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
	protected function buildAction()
	{
		// action path
		$actionPath = BASEPATH . 'default_www/' . $this->getLocation() . '/modules/' . strtolower($this->getModule()) . '/actions/' . $this->fileName;
		$templatePath = BASEPATH . 'default_www/' . $this->getLocation() . '/modules/' . strtolower($this->getModule()) . '/layout/templates/' . $this->templateName;

		// check if the action doesn't exist yet
		if(file_exists($actionPath)) throw new Exception('The action(' . $this->getLocation() . '/' .  strtolower($this->getModule()) . '/' . strtolower($this->actionName) . ') already exists.');

		// backend action
		if($this->getLocation() == 'backend')
		{
			// check if we need a specific base file
			$baseActions = array('add', 'edit', 'delete', 'settings');
			$baseAction = 'base';

			// loop the baseactions
			foreach($baseActions as $action)
			{
				// check if it is this action
				$tmpCheck = strpos($this->fileName, $action);
				if($tmpCheck !== false)
				{
					if($action == 'settings') $action = 'edit';
					$baseAction = $action;
				}
			}

			// insert info in the database to grant access
			if(!$this->databaseInfo()) return false;
		}
		// frontend aciton
		else
		{
			$baseAction = 'index';
		}

		// base file
		$baseFile = CLIPATH . 'knife/action/base/' . $this->getLocation() . '/' . $baseAction;

		// the action file
		$actionFile = $this->replaceFileInfo($baseFile . '.php');
		$this->makeFile($actionPath, $actionFile);

		// create template file, if we don't have a delete action
		if($baseAction != 'delete')
		{
			$actionTpl = $this->replaceFileInfo($baseFile . '.tpl');
			$this->makeFile($templatePath, $actionTpl);
		}

		return true;

		// @todo if it is a form action, build form via database(with table parameter)
		// @todo if it is an edit action, build form via add action(if exists)
		// @todo make it possible to choose the extension
	}

	/**
	 * Builds a specific location action
	 *
	 * @param	string $module		The module.
	 * @param	string $location	The working location.
	 * @param	string $actions		The actions to build
	 */
	private function buildLocationAction($module, $location, $actions = null)
	{
		// do we have a second action parameter?
		if(isset($location) && $actions == null) throw new Exception('Please provide an action name.');

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
			// build action variables
			$this->inputName = $action;
			$this->actionName = $this->buildName($action);
			$this->fileName = $this->buildFileName($action);
			$this->templateName = $this->buildFileName($action, 'tpl');

			// build the action
			$success = $this->buildAction();
			if($success) array_push($this->successArray, $this->actionName);
			else array_push($failArray, $this->actionName);
		}
	}

	/**
	 * Inserts a backend action into the database.
	 */
	private function databaseInfo()
	{
		// database instance
		$db = Knife::getDB(true);

		try
		{
			// set the parameters
			$parameters['group_id'] = 1;
			$parameters['module'] = strtolower($this->getModule());
			$parameters['action'] = strtolower($this->buildFileName($this->inputName, ''));
			$parameters['level'] = 7;

			// insert
			$db->insert('groups_rights_actions', $parameters);
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
		$fileInput = str_replace('actionname', $this->actionName, $fileInput);
		$fileInput = str_replace('versionname', VERSION, $fileInput);
		$fileInput = str_replace('authorname', AUTHOR, $fileInput);

		// return
		return $fileInput;
	}
}
