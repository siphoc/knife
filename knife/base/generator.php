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
	/**
	 * Creates a valid class name
	 *
	 * @return	string
	 * @param	string $name		The given name.
	 */
	public function buildName($name)
	{
		// lowercase
		$name = strtolower($name);

		// remove all non alphabetical or underscore characters
		$name = preg_replace("/[^a-zA-Z_\s]/", "", $name);

		// split the name on _
		$parts = explode('_', $name);

		// the new name
		$newName = '';

		// loop trough the parts to ucfirst it
		foreach($parts as $part)
		{
			$newName.= ucfirst($part);
		}

		// return
		return $newName;
	}

	/**
	 * Creates a valid file name
	 *
	 * @return	string
	 * @param	string $name				The given name.
	 * @param	string[optional] $ext		The file extension.
	 */
	public function buildFileName($name, $ext = 'php')
	{
		// lowercase
		$name = strtolower($name);
		$ext = strtolower($ext);

		// remove all non alphabetical or underscore characters
		$name = preg_replace("/[^a-z_\s]/", "", $name);
		$ext = preg_replace("/[^a-z\s]/", "", $ext);

		$newName = $name . '.' . $ext;

		// return
		return $newName;
	}
}
