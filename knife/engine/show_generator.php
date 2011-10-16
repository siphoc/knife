<?php

/**
 * This source file is a part of the Knife CLI Tool for Fork CMS.
 * More information can be found on http://www.fork-cms.com
 *
 * @package		knife
 *
 * @author		Jelmer Snoeck <jelmer.snoeck@netlash.com>
 * @since		0.4
 */
class KnifeShowGenerator extends KnifeBaseGenerator
{
	/**
	 * Execute the action. This is used to get data form the action but
	 * not doing anything with it.
	 */
	protected function execute()
	{
		$this->errorHandler(__CLASS__, 'init');
	}

	/**
	 * Usage: ft <command> [<args>]
	 *
	 * The most commonly used fork commands are(type ft help <command> for more info):
	 *   action      This creates an action(or multiple)
	 *   module      This creates module
	 *   theme       This creates a theme
	 *   show        This shows information about the command contents
	 *   settings    Adjusts a specific setting
	 */
	protected function init()
	{
		// the item to show
		$execution = $this->arg[0];

		// the function name
		$functionName = 'show' . ucfirst($execution);

		if(method_exists($this, $functionName)) $this->$functionName();
		else $this->errorHandler(__CLASS__, 'init');
	}

	/**
	 * Shows the version of the Fork project
	 */
	private function showVersion()
	{
		Knife::dump(VERSION);
	}

	/**
	 * Shows th modules
	 */
	private function showModules()
	{
		// start modules instance
		$modules = new KnifeModuleGenerator();
		$modules->showAll();
	}
}
