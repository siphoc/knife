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
	protected $actionName, $fileName, $templateName;

	/**
	 * Execute the action
	 */
	protected function init()
	{
		// set variables
		$this->setLocation($this->arg[0]);
		$this->setModule($this->arg[1]);
		$this->actionName = $this->buildName($this->arg[2]);
		$this->fileName = $this->buildFileName($this->arg[2]);
		$this->templateName = $this->buildFileName($this->arg[2], 'tpl');

		// build the action
		$this->buildAction();
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
		if(file_exists($actionPath)) throw new Exception('The action already exists');

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

			// base file
			$baseFile = CLIPATH . 'knife/action/base/' . $this->getLocation() . '/' . $baseAction;

			$actionFile = $this->replaceFileInfo($baseFile . '.php');
			$this->makeFile($actionPath, $actionFile);

			if($baseAction != 'delete')
			{
				$actionTpl = $this->replaceFileInfo($baseFile . '.tpl');
				$this->makeFile($templatePath, $actionTpl);
			}
		}
		// frontend aciton
		else
		{

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