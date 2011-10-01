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
	 * This executes the generator
	 */
	protected function init()
	{
		// the item to show
		$execution = $this->arg[0];

		try
		{
			// the class to search in
			$class = 'Knife' . ucfirst($execution) . 'Generator';

			// show info function callable?
			if(is_callable(array($class, 'showInfo')))
			{
				// call function
				$function = 'showInfo';

				// start new class
				$class = new $class();
				$class->$function();
			}
		}
		catch(Exception $e)
		{
			if(is_callable(array(__CLASS__, 'show' . ucfirst($execution))))
			{
				$function = 'show' . ucfirst($execution);
				$this->$function();
			}
			else throw new Exception('Invalid parameter');
		}
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
		$modules->showInfo();
	}
}
