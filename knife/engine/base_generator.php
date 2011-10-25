<?php

/**
 * This source file is a part of the Knife CLI Tool for Fork CMS.
 * More information can be found on http://www.fork-cms.com
 *
 * @package		knife
 *
 * @author		Jelmer Snoeck <jelmer.snoeck@netlash.com>
 * @since		0.6
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
	 * Options
	 *
	 * @var	array
	 */
	protected $options;

	/**
	 * The location
	 *
	 * @var	string
	 */
	protected $location;

	/**
	 * The module
	 *
	 * @var	string
	 */
	protected $module, $moduleFolder;

	/**
	 * Constructor
	 *
	 * @param array $arguments
	 */
	public function __construct(array $arguments = array(), array $options = array())
	{
		// set the arguments
		$this->arg = $arguments;

		// the options
		$this->options = $options;

		// initiate
		if(!empty($arguments)) $this->init();
		else $this->execute();
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
		$name = preg_replace("/[^a-z_\s]/", "", $name);

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

		// need extension?
		$newName = $name;
		$newName.= ($ext != '') ? '.' . $ext : '';

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
		foreach($parts as $part) $newName.= ucfirst($part);

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
		// get the documentation
		$reflectionClass = new ReflectionClass($class);
		$reflectionFunction = $reflectionClass->getMethod($function);
		$reflectionDocumentation = $reflectionFunction->getDocComment();

		// clean the documentation
		// @todo propper cleaning function
		$reflectionDocumentation = str_replace('	', '', $reflectionDocumentation);
		$reflectionDocumentation = str_replace('/**', '', $reflectionDocumentation);
		$reflectionDocumentation = str_replace(' * ', '', $reflectionDocumentation);
		$reflectionDocumentation = str_replace(' */', '', $reflectionDocumentation);
		$reflectionDocumentation = str_replace(' *', '', $reflectionDocumentation);

		// throw new exception
		throw new Exception($reflectionDocumentation);
	}

	/**
	 * The execute action, this will be used for a read action of an object.
	 */
	protected function execute() {}

	/**
	 * Fetches all the module names
	 *
	 * @return	array
	 */
	protected function getAllActiveModules()
	{
		try
		{
			// get the active modules
			$modules = Knife::getDB()->getColumn('SELECT m.name
													FROM modules AS m
													WHERE m.active = ?',
													array('Y'));
		}
		catch(Exception $e)
		{
			if(DEV_MODE) throw $e;
			else throw new Exception('Something went wrong while connecting to the database.');
		}

		// return
		return $modules;
	}

	/**
	 * Gets the location
	 *
	 * @return	string
	 */
	protected function getLocation()
	{
		return $this->location;
	}

	/**
	 * Gets the module
	 *
	 * @return	string
	 */
	protected function getModule()
	{
		return $this->module;
	}

	/**
	 * Gets the module
	 *
	 * @return	string
	 */
	protected function getModuleFolder()
	{
		return $this->moduleFolder;
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

	/**
	 * Reads the content of a file
	 *
	 * @return	string
	 * @param	string $file		The file path.
	 */
	protected function readFile($file)
	{
		// file exists?
		if(!file_exists($file)) throw new Exception('The given file(' . $file .') does not exist.');

		// open the file
		$oFile = fopen($file, 'r');

		// read the file
		$rFile = fread($oFile, filesize($file));

		// close the file
		fclose($oFile);

		// return
		return $rFile;
	}

	/**
	 * Sets the location
	 *
	 * @param	string $location		The location.
	 */
	protected function setLocation($location)
	{
		// set short terms
		if($location == 'f') $location = 'frontend';
		if($location == 'b') $location = 'backend';

		// set the location
		if($location == 'frontend' || $location == 'backend') $this->location = (string) strtolower($location);
		// not a valid location
		else throw new Exception('This(' . $location . ') is not a valid location');
	}

	/**
	 * Sets the module
	 *
	 * @param	string $module		The module.
	 */
	protected function setModule($module)
	{
		// module exists?
		if(is_dir(BASEPATH . 'default_www/' . $this->location . '/modules/' . $module))
		{
			$this->module = (string) $this->buildName($module);
			$this->moduleFolder = $this->buildDirName($module);
		}
		// doesnt exist
		else throw new Exception('The given module(' . $module . ') does not exist.');
	}

	/**
	 * The success handler. This shows a success message.
	 *
	 * @param	string $message		The message to show.
	 */
	protected function successHandler($message)
	{
		echo $message . "\n";
	}
}
