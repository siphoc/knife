<?php

/**
 * This source file is a part of the Knife CLI Tool for Fork CMS.
 * More information can be found on http://www.fork-cms.com
 *
 * @package		knife
 *
 * @author		Jelmer Snoeck <jelmer.snoeck@netlash.com>
 * @since		0.1
 */

/*
 * This checks if we're running from the command line
 * and prints an error if not.
 */
if(PHP_SAPI !== 'cli') die('We expect this to be running on the command line.');

/*
 * This is the version number of the current CLI Tool
 */
define('KNIFE_VERSION', '0.1');

/*
 * Set error reporting
 */
if(DEV_MODE) error_reporting(E_ALL);
else error_reporting(0);

/*
 * Get the exception class
 */
require_once 'exception/exception.php';

/*
 * Set the autoloader
 */
spl_autoload_register(array('Knife', 'autoLoader'));

/**
 * This class contains all generic functions for the knife libary
 *
 * @package		knife
 *
 *
 * @author		Jelmer Snoeck <jelmer.snoeck@netlash.com>
 * @since		0.1
 */
class Knife
{
	/**
	 * The user
	 *
	 * @var	string
	 */
	public static $author;

	/**
	 * The version
	 *
	 * @var	string
	 */
	public static $version;

	/**
	 * The database
	 *
	 * @var	SpoonDatabase
	 */
	private static $db;

	/**
	 * This is the constructor for the CLI Tool
	 *
	 * @param	array $argv		The arguments passed by the command line.
	 */
	public function __construct($argv)
	{
		// do startup checks
		$this->startChecks();

		// build the paths
		$this->buildPaths();

		// no argument set?
		if(!isset($argv[1])) throw new Exception('Please, specify an action.');

		/* Spoon stuff */
		require_once LIBRARYPATH . 'globals.php';

		// set the class to call
		$callClass = 'Knife' . ucfirst($argv[1]) . 'Generator';

		// arguments
		$arguments = $argv;
		unset($arguments[0]);
		unset($arguments[1]);

		// rebase
		$passArgs = array();
		foreach($arguments as $parameter) $passArgs[] = $parameter;

		// execute the action
		$tmpClass = new $callClass($passArgs);
	}

	/**
	 * The knife autoloader
	 *
	 * @param string $class
	*/
	public static function autoLoader($class)
	{
		// make the class lowercase
		$tmpClass = strtolower($class);

		/*
		 * Dirty classes job
		 *
		 * @todo use namespaces (ask Davy)
		 */
		$classes = array();
		$classes['knifebasegenerator'] = CLIPATH . 'knife/engine/base_generator.php';
		$classes['knifethemegenerator'] = CLIPATH . 'knife/theme/generator.php';
		$classes['knifemodulegenerator'] = CLIPATH . 'knife/module/generator.php';
		$classes['knifeactiongenerator'] = CLIPATH . 'knife/action/generator.php';
		$classes['knifedatabase'] = CLIPATH . 'knife/database/database.php';
		$classes['knifeshowgenerator'] = CLIPATH . 'knife/engine/show_generator.php';

		// is the class set?
		if(!array_key_exists($tmpClass, $classes))
			throw new Exception('This class(' . $class . ') isn\'t set in Knife.');

		// does the file exist?
		if(!file_exists($classes[$tmpClass]))
			throw new Exception('The class file(' . $classes[$tmpClass] . ') doesn\'t exist.');

		// get the file
		require_once $classes[$tmpClass];

		// is the class callable?
		if(!class_exists($class, false))
			throw new Exception('The file is present but the class name should be ' . $class);
	}

	/**
	 * Checks if we are working in a valid Fork dir
	 *
	 * @return	bool
	 */
	private function buildPaths()
	{
		// set the working dir
		$workingDir = getcwd();

		// are we in default_www or library?
		$posDefWWW = strpos($workingDir, 'default_www');
		$posDefLib = strpos($workingDir, 'library');

		// we're not in one of forks working dirs
		if(empty($posDefWWW) && empty($posDefLib))
		{
			// is there a library path and default_www path available?
			if(!is_dir($workingDir . '/default_www') && !is_dir($workingDir . '/library'))
			{
				throw new Exception('This is not a valid Fork NG path. Please initiate in your home folder of your project.');
			}

			// create working paths
			$frontendPath = $workingDir . '/default_www/frontend/';
			$backendPath = $workingDir . '/default_www/backend/';
			$libraryPath = $workingDir . '/library/';
			$basePath = $workingDir . '/';
		}
		// we're in one
		else
		{
			// where to split on
			$splitChar = (!empty($posDefWWW)) ? 'default_www' : 'library';

			// split the directory to go into default_www
			$workingDir = explode($splitChar, $workingDir);
			$workingDir = $workingDir[0];

			// create paths
			$frontendPath = $workingDir . 'default_www/frontend/';
			$backendPath = $workingDir . 'default_www/backend/';
			$libraryPath = $workingDir . 'library/';
			$basePath = $workingDir;
		}

		// read the version
		$oVersion = fopen($basePath . 'VERSION.md', 'r');
		$rVersion = fread($oVersion, filesize($basePath . 'VERSION.md'));
		define('VERSION', trim($rVersion));
		$rVersion = (int) str_replace('.', '', $rVersion);

		// check if the frontend and backend exist (old fork doesn't have this)
		if(!is_dir($frontendPath) || !is_dir($backendPath) || $rVersion < 200)
		{
			throw new Exception('This is an older version of Fork. The Fork tool only works with V2+.');
		}

		// set paths for overall use
		define('FRONTENDPATH', $frontendPath);
		define('BACKENDPATH', $backendPath);
		define('BASEPATH', $basePath);
		define('LIBRARYPATH', $libraryPath);
	}

	/**
	 * Checks the settings
	 */
	private function checkSettings()
	{
		// settings path
		$settingsPath = CLIPATH . '.ftconfig';

		// does the file exist?
		if(!file_exists($settingsPath)) throw new Exception('You have no settings file. Please change your settings. This will automaticly make the file.');

		// opens the file (creates one if it doesn't exists)
		$oFile = fopen($settingsPath, 'r');
		$rFile = fread($oFile, filesize($settingsPath));

		// author
		$author = preg_match('/#author=(.*);/', $rFile, $authorMatch);
		define('AUTHOR', $authorMatch[1]);

		// close the file
		fclose($oFile);
	}

	/**
	 * Initiates the stuff for devers
	 */
	private function devStart()
	{
		// set paths for overall use
		define('FRONTENDPATH', CLIPATH . 'devdir/');
		define('BACKENDPATH', CLIPATH . 'devdir/');
	}

	/**
	 * Dumps the text
	 *
	 * @param	string $text			The text to dump.
	 * @param	bool[optional] $exit	Exit or not.
	 */
	public static function dump($var, $exit = true)
	{
		// start output buffering
		ob_start();
		var_dump($var);
		$output = ob_get_clean();

		// no xdebug installed
		if(!extension_loaded('xdebug'))
		{
			// put array on one line
			$output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
		}

		// print output
		echo "-----------------------------------------DUMP-----------------------------------------\n";
		echo $output;
		echo "-----------------------------------------DUMP-----------------------------------------\n";

		// exit
		if($exit) exit;
	}

	/**
	 * Gets the database instance
	 *
	 * @return	SpoonDatabase
	 * @write	bool[optional] $write		Write to the database or not.
	 */
	public static function getDB($write = true)
	{
		// redefine
		$write = (bool) $write;

		// do we have a db-object ready?
		if(!isset(self::$db))
		{
			// create instance
			$db = new KnifeDatabase(DB_TYPE, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);

			// utf8 compliance & MySQL-timezone
			$db->execute('SET CHARACTER SET utf8, NAMES utf8, time_zone = "+0:00"');

			// store
			self::$db = $db;
		}

		// return db-object
		return self::$db;
	}

	/**
	 * This does the initial checks
	 */
	private function startChecks()
	{
		$this->checkSettings();
	}
}
