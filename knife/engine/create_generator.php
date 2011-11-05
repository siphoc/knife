<?php

/**
 * This source file is a part of the Knife CLI Tool for Fork CMS.
 * More information can be found on http://www.fork-cms.com
 *
 * @package		knife
 * @subpackage	create
 *
 * @author		Jelmer Snoeck <jelmer.snoeck@netlash.com>
 * @since		0.9
 */
class KnifeCreateGenerator extends KnifeBaseGenerator
{
	/**
	 * This starts the generator.
	 */
	public function init()
	{
		// name given?
		if(!isset($this->arg[0])) throw new Exception('Please specify an action to generate.');

		if($this->arg[0] == 'info') $this->createInfoFile();
		elseif($this->arg[0] == 'globals') $this->createGlobals();
	}

	/**
	 * This will generate the info.xml file from the module files. It will go trough
	 * them and search for triggers.
	 */
	protected function createInfoFile()
	{

	}

	/**
	 * This will create the global files to easily start a new project localy
	 */
	protected function createGlobals()
	{

	}
}
