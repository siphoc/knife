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
			$actionData = $this->buildActionData($data);

			// build the action
			$this->buildLocationAction($this->arg[0], $actionData['location'], $actionData['actions']);

			// print the good actions
			if(empty($this->failArray)) $this->successHandler('The ' . $this->getLocation() . ' actions are created.');
			else $this->errorHandler(__CLASS__, 'buildAction');
		}
	}

	/**
	 * Build the action info
	 *
	 * @return	array
	 * @param	string $data		The data to convert into an array.
	 */
	private function buildActionData($data)
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
	 * This will add a block to a specific action
	 */
	private function addBlock()
	{
		// try adding the block
		try
		{
			// do we have an extra already?
			$existExtra = (bool) Knife::getDB()->getVar('SELECT COUNT(m.id)
															FROM pages_extras AS m
															WHERE m.module = ? AND m.action = ? AND m.type = ?',
															array((string) strtolower($this->getModule()), (string) substr($this->fileName, 0, -4), 'block'));

			// we have no extra yet
			if(!$existExtra)
			{
				// set next sequence number for this module
				$sequence = Knife::getDB()->getVar('SELECT MAX(sequence) + 1 FROM pages_extras WHERE module = ?', array((string) strtolower($this->getModule())));

				// this is the first extra for this module: generate new 1000-series
				if(is_null($sequence)) $sequence = $sequence = Knife::getDB()->getVar('SELECT CEILING(MAX(sequence) / 1000) * 1000 FROM pages_extras');

				// the data
				$data['module'] = strtolower($this->getModule());
				$data['type'] = 'block';
				$data['label'] = $this->actionName;
				$data['action'] = substr($this->fileName, 0, -4);
				$data['sequence'] = $sequence;

				// insert
				Knife::getDB(true)->insert('pages_extras', $data);
			}
		}
		// we have errors
		catch(Exception $e)
		{
			// dev mode
			if(DEV_MODE) throw $e;

			// no dev mode, return false
			return false;
		}

		// return
		return true;
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
		// the location path
		$locationPath = ($this->getLocation() == 'frontend') ? FRONTENDPATH : BACKENDPATH;

		// action path
		$actionPath = $locationPath . 'modules/' . $this->getModuleFolder() . '/actions/' . $this->fileName;
		$templatePath = $locationPath . 'modules/' . $this->getModuleFolder() . '/layout/templates/' . $this->templateName;

		// check if the action doesn't exist yet
		if(file_exists($actionPath) && !$this->addBlock) throw new Exception('The action(' . $this->getLocation() . '/' .  $this->getModuleFolder() . '/' . strtolower($this->actionName) . ') already exists.');

		// if we only need to add a block, return that value
		if(file_exists($actionPath) && $this->addBlock) return $this->addBlock();

		// backend action
		if($this->getLocation() == 'backend')
		{
			// check if we need a specific base file
			$baseActions = array('add', 'edit', 'delete', 'settings', 'index');
			$baseAction = 'base';

			// loop the baseactions
			foreach($baseActions as $action)
			{
				// check if it is this action
				$tmpCheck = strpos($this->fileName, $action);
				if($tmpCheck !== false)
				{
					// a setting page is an edit action
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
			// set the base action
			$baseAction = 'index';

			// add the block
			if($this->addBlock) $this->addBlock();
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

		// return
		return true;

		// @todo if it is a form action, build form via database(with table parameter)
		// @todo if it is an edit action, build form via add action(if exists, else database)
	}

	/**
	 * Builds a specific location action
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
			// its the backend
			else
			{
				// generate install.php action data
				$actionSetting = '$this->setActionRights(1, \'' . $this->getModuleFolder() . '\', \'' . $this->buildName($action) . '\')';

				// read the installer into an array
				$aInstall = file(BACKENDPATH . 'modules/' . $this->getModuleFolder() . '/installer/install.php');

				// the new file array
				$fileArray = array();

				// fileKey
				$fileKey = 0;

				// loop the installer lines
				foreach($aInstall as $key => $line)
				{
					// trim the line
					$trimmedLine = trim($line);

					// get the index action, this should always be present
					if($trimmedLine == '$this->setActionRights(1, \'' . $this->getModuleFolder() . '\', \'index\');')
					{
						// the new rule
						$fileArray[$fileKey] = "\t\t" . '$this->setActionRights(1, \'' . $this->getModuleFolder() . '\', \'' . $action . '\');' . "\n";

						// reset the line key
						$fileKey++;
					}

					// add the line
					$fileArray[$fileKey] = $line;

					// count up
					$fileKey++;
				}

				// rewrite the file
				file_put_contents(BACKENDPATH . 'modules/' . $this->getModuleFolder() . '/installer/install.php', $fileArray);
			}

			// build action variables
			$this->inputName = $action;
			$this->actionName = $this->buildName($action);
			$this->fileName = $this->buildFileName($action);
			$this->templateName = $this->buildFileName($action, 'tpl');

			// build the action
			$success = $this->buildAction();
			if($success) array_push($this->successArray, $this->actionName);
			else array_push($this->failArray, $this->actionName);
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
			$parameters['module'] = $this->getModuleFolder();
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
		$fileInput = str_replace('subname', $this->getModuleFolder(), $fileInput);
		$fileInput = str_replace('actionname', $this->actionName, $fileInput);
		$fileInput = str_replace('versionname', VERSION, $fileInput);
		$fileInput = str_replace('authorname', AUTHOR, $fileInput);

		// return
		return $fileInput;
	}
}
