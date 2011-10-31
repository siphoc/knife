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
	 * This recursivly adds files to the given zip file
	 *
	 * @param ZipArchive $zipArchive
	 * @param string directory
	 * @return ZipArchive
	 */
	private function addToArchive(ZipArchive $zipArchive, $directory, $putInto)
	{
		if($handle = opendir($directory))
		{
			while(($file = readdir($handle)) !== false)
			{
				if(is_file($directory . $file))
				{
					$fileContents = file_get_contents($directory . $file);
					$zipArchive->addFile($directory . $file, $putInto . $file);
				}
				elseif($file != '.' && $file != '..' && $file != '.svn')
				{
					$zipArchive->addEmptyDir($putInto . $file . '/');
					$this->addToArchive($zipArchive, $directory . $file . '/', $putInto . $file . '/');
				}
			}
		}
		closedir($handle);

		return $zipArchive;
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
		$this->setModule($this->arg[1]);

		$zipFile = new ZipArchive();
		if($zipFile->open(BASEPATH . $this->getModuleFolder() . '.zip', ZipArchive::CREATE))
		{
			if(is_dir(FRONTENDPATH . 'modules/' . $this->getModuleFolder() . '/'))
			{
				$zipFile->addEmptyDir('frontend');
				$zipFile = $this->addToArchive($zipFile, FRONTENDPATH . 'modules/' . $this->getModuleFolder() . '/', 'frontend/');
			}

			if(is_dir(BACKENDPATH . 'modules/' . $this->getModuleFolder()))
			{
				$zipFile->addEmptyDir('backend');
				$zipFile = $this->addToArchive($zipFile, BACKENDPATH . 'modules/' . $this->getModuleFolder() . '/', 'backend/');
			}
		}
		$zipFile->close();
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
