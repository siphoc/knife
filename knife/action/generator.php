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
	protected $actionName, $fileName, $templateName, $inputName;

	/**
	 * Execute the action
	 */
	protected function init()
	{
		// @todo make error for no input
		// @todo make success message

		// name given?
		if(!isset($this->arg[2])) throw new Exception('Please provide an action name.');

		// set variables
		$this->setLocation($this->arg[1]);
		$this->setModule($this->arg[0]);

		// variables
		$actionNames = str_replace(' ', '', $this->arg[2]);
		$actionNames = explode(',', $actionNames);

		foreach($actionNames as $action)
		{
			// build action variables
			$this->inputName = $action;
			$this->actionName = $this->buildName($action);
			$this->fileName = $this->buildFileName($action);
			$this->templateName = $this->buildFileName($action, 'tpl');

			// build the action
			$this->buildAction();
		}

		if(!isset($this->arg[3])) return;

		// do we have a second action parameter?
		if(isset($this->arg[3]) && !isset($this->arg[4])) throw new Exception('Please provide an action name.');

		// set variables
		$this->setLocation($this->arg[3]);
		$this->setModule($this->arg[0]);

		$actionNames = str_replace(' ', '', $this->arg[4]);
		$actionNames = explode(',', $actionNames);

		foreach($actionNames as $action)
		{
			// build action variables
			$this->inputName = $action;
			$this->actionName = $this->buildName($action);
			$this->fileName = $this->buildFileName($action);
			$this->templateName = $this->buildFileName($action, 'tpl');

			// build the action
			$this->buildAction();
		}
	}

	/**
	 * This will generate an action. It willn ot overwrite an existing action.
	 *
	 *
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
			$baseActions = array('add', 'edit', 'delete');
			$baseAction = 'base';

			// loop the baseactions
			foreach($baseActions as $action)
			{
				// check if it is this action
				$tmpCheck = strpos($this->fileName, $action);
				if($tmpCheck !== false) $baseAction = $action;
			}

			// insert info in the database to grant access
			if(!$this->databaseInfo()) return;
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
	}

	/**
	 * Inserts a backend action into the database.
	 *
	 * @return	void
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
			throw new Exception('Something went wrong while inserting the data into the database.');
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