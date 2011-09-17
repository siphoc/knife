<?php
/**
 * This source file is a part of Fork CMS.
 * More information can be found on http://www.fork-cms.com
 *
 * @package		knife
 *
 * @author		Jelmer Snoeck <jelmer.snoeck@netlash.com>
 * @since		0.1
 */

class KnifeBaseGenerator
{
	public function buildName($name)
	{
		return $name;
		return strtolower($name);
	}
}
