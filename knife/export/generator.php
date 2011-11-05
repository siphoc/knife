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
				$zipFile->addEmptyDir('frontend/modules');
				$zipFile->addEmptyDir('frontend/modules/' . $this->getModuleFolder());
				$zipFile = $this->addToArchive($zipFile, FRONTENDPATH . 'modules/' . $this->getModuleFolder() . '/', 'frontend/modules/' . $this->getModuleFolder() . '/');
			}

			if(is_dir(BACKENDPATH . 'modules/' . $this->getModuleFolder()))
			{
				$zipFile->addEmptyDir('backend');
				$zipFile->addEmptyDir('backend/modules');
				$zipFile->addEmptyDir('backend/modules/' . $this->getModuleFolder());
				$zipFile = $this->addToArchive($zipFile, BACKENDPATH . 'modules/' . $this->getModuleFolder() . '/', 'backend/modules/' . $this->getModuleFolder() . '/');
			}
		}
		$zipFile->close();

		// print success message
		echo 'The module is exported. You can find it at ' . BASEPATH . $this->getModuleFolder() . '.zip' . "\n";
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

		// the directory name
		$dirName = $this->buildDirName($this->arg[1]);
		$themePath = FRONTENDPATH . 'themes/' . $dirName;
		$db = Knife::getDB();

		if(!is_dir(FRONTENDPATH . 'themes/' . $dirName)) throw new Exception('Please provide an existing theme name');

		// there is no info file, create one
		if(!file_exists($themePath . '/info.xml'))
		{
			$themeInfo = $db->getRecords(
				'SELECT t.*
				 FROM themes_templates AS t
				 WHERE t.theme = ?',
				array($dirName)
			);

			// no templated, we need those!
			if(empty($themeInfo)) throw new Exception('There are no templates for this theme');

			// this will be the generated template information
			$templateString = '';
			foreach($themeInfo as $template)
			{
				$templateName = $template['label'];
				$templateData = unserialize($template['data']);
				$templatePath = $template['path'];
				$defaultExtras = (isset($templateData['default_extras'])) ? $templateData['default_extras'] : array();

				$templateString.= '<template label="' . $templateName . '" path="' . $templatePath .'">' . "\n";
				$templateString.= "\t<positions>" . "\n";
				foreach($templateData['names'] as $position)
				{
					if(isset($defaultExtras[$position]))
					{
						$templateString.= "\t\t" . '<position name="' . $position . '">' . "\n";
						$templateString.= "\t\t\t<defaults>" . "\n";

						// fetch the default info
						$defaultInfo = $db->getRecord(
							'SELECT e.*
							 FROM modules_extras AS e
							 WHERE e.id = ?',
							array((int) $defaultExtras[$position][0])
						);
						$templateString.= "\t\t\t\t" . '<' . $defaultInfo['type'] . ' module="' . $defaultInfo['module'] . '" action="' . $defaultInfo['action'] . '" />' . "\n";

						$templateString.= "\t\t\t</defaults>" . "\n";
						$templateString.= "\t\t</position>" . "\n";
					}
					else $templateString.= "\t\t" . '<position name="' . $position . '" />' . "\n";
				}
				$templateString.= "\t</positions>" . "\n";
				$templateString.= "\t<format>" . "\n";
				$templateString.= "\t\t" . $templateData['format'] . "\n";
				$templateString.= "\t</format>" . "\n";
				$templateString.= '</template>' . "\n";
			}

			// replace the basic values and save the file
			$basicFile = $this->readFile(CLIPATH . 'knife/theme/base/info.xml');
			$basicFile = str_replace('themename', $dirName, $basicFile);
			$basicFile = str_replace('forkversion', VERSION, $basicFile);
			$basicFile = str_replace('authorname', AUTHORNAME, $basicFile);
			$basicFile = str_replace('authorurl', AUTHORURL, $basicFile);
			$basicFile = str_replace('created_templates', $templateString, $basicFile);
			$this->makeFile($themePath . '/info.xml', $basicFile);
		}

		$zipFile = new ZipArchive();
		if($zipFile->open(BASEPATH . $dirName . '.zip', ZipArchive::CREATE))
		{
			$zipFile = $this->addToArchive($zipFile, $themePath . '/', '/');
		}
		$zipFile->close();

		// print success message
		echo 'The theme is exported. You can find it at ' . BASEPATH . $dirName . '.zip' . "\n";
	}
}
