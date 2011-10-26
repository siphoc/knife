<?php

/**
 * This source file is a part of the Knife CLI Tool for Fork CMS.
 * More information can be found on http://www.fork-cms.com
 *
 * @package		knife
 * @subpackage	export
 *
 * @author		Jelmer Snoeck <jelmer.snoeck@netlash.com>
 * @since		0.7
 */
class KnifeExportGenerator extends KnifeBaseGenerator
{
	/**
	 * This starts the generator.
	 */
	public function init()
	{
		// name given?
		if(!isset($this->arg[0])) throw new Exception('Please specify an export action');

		// export of a module
		if($this->arg[0] == 'module') $this->exportModule();
		elseif($this->arg[0] == 'theme') $this->exportTheme();
		else throw new Exception('Invalid action');
	}

	/**
	 * This action will export a module so it can easily be transferred to install somewhere
	 * else.
	 *
	 * Required parameters: 'modulename'
	 *
	 * Example:
	 * ft export module blog
	 */
	private function exportModule()
	{
		// no module name given
		if(!isset($this->arg[1])) throw new Exception('Please provide a module name');

		// get the files
	}

	/**
	 * This action will export a theme so it can easily be transferred to install somewhere
	 * else.
	 *
	 * Required parameters: 'themename'
	 *
	 * Example:
	 * ft export theme triton
	 */
	private function exportTheme()
	{
		// no module name given
		if(!isset($this->arg[1])) throw new Exception('Please provide a theme name');

		// get the files
	}
}
