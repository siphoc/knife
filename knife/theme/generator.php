<?php

/**
 * This source file is a part of the Knife CLI Tool for Fork CMS.
 * More information can be found on http://www.fork-cms.com
 *
 * @package		knife
 * @subpackage	theme
 *
 * @author		Jelmer Snoeck <jelmer.snoeck@netlash.com>
 * @since		0.1
 */
class KnifeThemeGenerator extends KnifeBaseGenerator
{
	/**
	 * The theme name
	 *
	 * @var	string
	 */
	private $themeName;

	/**
	 * Template string
	 *
	 * @var	string
	 */
	private $dbTemplateData = 'a:3:{s:6:"format";s:71:"[/,/,9,9],[/,/,10,10],[/,/,/,/],[5,1,1,1],[6,2,2,2],[7,3,3,3],[8,4,4,4]";s:5:"names";a:10:{i:0;s:6:"Editor";i:1;s:6:"Editor";i:2;s:6:"Editor";i:3;s:6:"Editor";i:4;s:6:"Widget";i:5;s:6:"Widget";i:6;s:6:"Widget";i:7;s:6:"Widget";i:8;s:22:"Advertisement (468x60)";i:9;s:6:"Search";}s:14:"default_extras";a:10:{i:0;s:6:"editor";i:1;s:6:"editor";i:2;s:6:"editor";i:3;s:6:"editor";i:4;s:6:"editor";i:5;s:6:"editor";i:6;s:6:"editor";i:7;s:6:"editor";i:8;s:6:"editor";i:9;i:1;}}';
	private $dbNumBlocks = 10;
	private $dbPath = 'core/layout/templates/default.tpl';
	private $dbLabel = 'Default';

	/**
	 * This executes the generator
	 */
	protected function init()
	{
		// name given?
		if(!isset($this->arg[0])) throw new Exception('Please specify a theme name');

		// initiate name
		$this->themeName = $this->cleanString($this->arg[0]);

		// create the theme
		$return = $this->createTheme();

		if(!$return) $this->errorHandler(__CLASS__, 'createTheme');
		else $this->successHandler('The theme "' . ucfirst($this->themeName) . '" is created.');
	}

	/**
	 * Creates the database info
	 */
	private function createDatabaseInfo()
	{
		// build the parameters
		$parameters = array();
		$parameters['theme'] = $this->themeName;
		$parameters['label'] = $this->dbLabel;
		$parameters['path'] = $this->dbPath;
		$parameters['num_blocks'] = $this->dbNumBlocks;
		$parameters['data'] = $this->dbTemplateData;

		// return
		return (bool) Knife::getDB(true)->insert('pages_templates', $parameters);
	}

	/**
	 * Create the directories
	 */
	private function createDirs()
	{
		// tree structure of the directory
		$dirs = array(
					'main' => FRONTENDPATH . 'themes/' . $this->themeName,
					'sub' => array(
								'core' => array(
											'js',
											'layout' => array(
														'css',
														'fonts',
														'images',
														'templates'
											)
								),
								'modules'
					)
		);

		// create the directories
		$this->makeDirs($dirs);
	}

	/**
	 * Create the base files
	 */
	protected function createFiles()
	{
		// triton
		$tritonPath = FRONTENDPATH . 'themes/triton/core/';
		$newPath = FRONTENDPATH . 'themes/' . $this->themeName . '/core/';

		// create from triton
		if(is_dir($tritonPath))
		{
			// copy existing files
			copy($tritonPath . 'js/html5.js', $newPath . 'js/html5.js');
			copy($tritonPath . 'layout/templates/default.tpl', $newPath . 'layout/templates/default.tpl');
			copy($tritonPath . 'layout/templates/footer.tpl', $newPath . 'layout/templates/footer.tpl');
			copy($tritonPath . 'layout/templates/head.tpl', $newPath . 'layout/templates/head.tpl');
		}
		// create empty files
		else
		{
			// create new files
			copy(FRONTENDPATH . 'core/js/html5.js', $newPath . 'js/html5.js');
			$this->makeFile($newPath . 'layout/templates/default.tpl');
			$this->makeFile($newPath . 'layout/templates/footer.tpl');
			$this->makeFile($newPath . 'layout/templates/head.tpl');
		}

		// create new files
		$this->makeFile($newPath . 'js/' . $this->themeName . '.js');
		$this->makeFile($newPath . 'layout/css/screen.css');
	}

	/**
	 * This action will create a theme. This will not overwrite an existing theme.
	 *
	 * The data needed to create a theme: 'themename'
	 *
	 * Example: ft theme 'knife'
	 */
	protected function createTheme()
	{
		// check if the the theme exists
		if(is_dir(FRONTENDPATH . 'themes/' . $this->themeName)) return false;

		// create the dirs
		$this->createDirs();

		// create the files
		$this->createFiles();

		// set info in the database
		if(!$this->createDatabaseInfo()) return false;

		// return
		return true;
	}
}
