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
	 * The arguments
	 *
	 * @var	array
	 */
	protected $arg;

	/**
	 * Constructor
	 *
	 * @param array $arguments
	 */
	public function __construct(array $arguments)
	{
		// set the arguments
		$this->arg = $arguments;

		// initiate
		$this->init();
	}

	/**
	 * Creates a valid file name
	 *
	 * @return	string
	 * @param	string $name				The given name.
	 */
	public function buildDirName($name)
	{
		// lowercase
		$name = strtolower($name);

		// remove all non alphabetical or underscore characters
		$name = preg_replace("/[^a-z\s]/", "", $name);

		// return
		return $name;
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
	 * Removes the spaces from a string
	 *
	 * @return	string
	 * @param	string $string		The string to edit.
	 */
	public function cleanString($string)
	{
		return str_replace(' ', '', $string);
	}

	/**
	 * The error handler. This gets the PHPDoc from the create function and prints
	 * it in a proper way.
	 *
	 * @param	string $class		The class to search in.
	 * @param	string $function	The function to search.
	 */
	protected function errorHandler($class, $function)
	{
		$reflectionClass = new ReflectionClass($class);
		$reflectionFunction = $reflectionClass->getMethod($function);
		$reflectionDocumentation = $reflectionFunction->getDocComment();

		throw new Exception($reflectionDocumentation);
	}

	/**
	 * The init function, this sets the needed variable names
	 * and calls the required actions.
	 */
	protected function init() {}

	/**
	 * Creates the directories from a given array
	 *
	 * @param	array $dirs		The directories to create
	 */
	protected function makeDirs(array $dirs)
	{
		// the main dir
		$mainDir = '';

		// loop the directories
		foreach($dirs as $type => $dir)
		{
			// create a new dir if this is the main dir
			if($type == 'main')
			{
				mkdir($dir);
				$mainDir = $dir . '/';
				continue;
			}

			// loob the dir to check for subdirs if this isn't the main
			foreach($dir as $name => $subdir)
			{
				// no subdirs
				if(!is_array($subdir)) mkdir($mainDir . $subdir);
				// more subdirs
				else
				{
					// create new array to pass
					$tmpArray = array(
									'main' => $mainDir . $name,
									'sub' => $subdir
					);

					// make the dir
					$this->makeDirs($tmpArray);
				}
			}
		}
	}

	/**
	 * Creates a file in a specific directory
	 *
	 * @param	string $file				The file name.
	 * @param	string[optional] $input		The input for the file.
	 */
	protected function makeFile($file, $input = null)
	{
		// create the file
		$oFile = fopen($file, 'w');

		// input?
		if($input !== null) fwrite($oFile, $input);

		// close the file
		fclose($oFile);
	}
}
