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

		if(class_exists('Knife' . ucfirst($execution) . 'Generator'))
		{
			Knife::dump('true');
		}
		else
		{
			Knife::dump('false');
		}
	}
}
