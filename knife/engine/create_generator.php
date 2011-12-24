<?php

/**
 * This source file is a part of the Knife CLI Tool for Fork CMS.
 * More information can be found on http://www.fork-cms.com
 *
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */
class KnifeCreateGenerator extends KnifeBaseGenerator
{
	/**
	 * This starts the generator.
	 */
	public function init()
	{
		// name given?
		if(!isset($this->arg[0])) throw new Exception('Please specify an action to generate.');

		if($this->arg[0] == 'info') $this->createInfoFile();
		elseif($this->arg[0] == 'globals') $this->createGlobals();
	}

	/**
	 * This will build the trigger data from an array of trigger events
	 *
	 * @param array $triggers
	 * @param string $location
	 * @return string
	 */
	protected function buildTriggerData(array $triggers, $location)
	{
		$returnString = '';

		foreach($triggers as $event)
		{
			$eventData = explode(',', $event);
			$eventName = trim($eventData[1]);
			$eventName = str_replace('\'', '', $eventName);

			$returnString.= "\t\t" . '<event application="' . $location . '" name="' . $eventName . '"><![CDATA[Triggered when ' . $eventName . '.]]></event>' . "\n";
		}

		return $returnString;
	}

	/**
	 * This will generate the info.xml file from the module files. It will go trough
	 * them and search for triggers.
	 */
	protected function createInfoFile()
	{
		if(!isset($this->arg[1])) throw new Exception('Please specify a module to create the info file for.');
		$this->setModule($this->arg[1]);

		$triggerData = '';

		$frontendTriggers = array();
		$backendTriggers = array();
		$frontendTriggers = $this->searchDirectoryTriggers($frontendTriggers, FRONTENDPATH . 'modules/' . $this->getModuleFolder() . '/');
		$backendTriggers = $this->searchDirectoryTriggers($frontendTriggers, BACKENDPATH . 'modules/' . $this->getModuleFolder() . '/');
		if(!empty($frontendTriggers)) $triggerData.= $this->buildTriggerData($frontendTriggers, 'frontend');
		if(!empty($backendTriggers)) $triggerData.= $this->buildTriggerData($backendTriggers, 'backend');

		// replace the events
		$infoFile = $this->readFile(BACKENDPATH . 'modules/' . $this->getModuleFolder() . '/info.xml');
		$fileContents = preg_replace('/<event (.*)>\n/', '', $infoFile);
		$fileContents = preg_replace('/(.*)<\/events>/', '</events>', $fileContents);
		$fileContents = preg_replace('/<events>(.*)\n/', '<events>', $fileContents);
		$fileContents = preg_replace('/<events><\/events>\n/', "<events>\n" . $triggerData . "\t</events>\n", $fileContents);
		file_put_contents(BACKENDPATH . 'modules/' . $this->getModuleFolder() . '/info.xml', $fileContents);

		echo 'The info.xml file for the ' . $this->getModule() . ' module is succesfully created.' . "\n";
	}

	/**
	 * This will create the global files to easily start a new project localy
	 */
	protected function createGlobals()
	{
		// create the specified git hooks if this projects uses git
		if(is_dir(BASEPATH . '.git/hooks'))
		{
			// define the base paths
			$preCommitSample = CLIPATH . 'knife/engine/base/pre-commit.sample';
			$postCheckoutSample = CLIPATH . 'knife/engine/base/post-checkout.sample';
			$gitHooksDir = BASEPATH . '.git/hooks/';

			// copy the files
			copy($preCommitSample, $gitHooksDir . 'pre-commit');
			copy($postCheckoutSample, $gitHooksDir . 'post-checkout');
		}

		/*
		 * Create the globals
		 */

		// define the base locations
		$globalsFrontendBase = LIBRARYPATH . 'globals_frontend.base.php';
		$globalsFrontend = LIBRARYPATH . 'globals_frontend.php';
		$globalsBackendBase = LIBRARYPATH . 'globals_backend.base.php';
		$globalsBackend = LIBRARYPATH . 'globals_backend.base.php';
		$globalsBase = CLIPATH . 'knife/engine/base/globals.base.php';
		$globals = LIBRARYPATH . 'globals.php';

		// copy those that can be copied
		copy($globalsFrontendBase, $globalsFrontend);
		copy($globalsBackendBase, $globalsBackend);

		// get the globals content to replace it with some data
		$globalsContent = file_get_contents($globalsBase);
		$globalsContent = str_replace('<projectname>', PROJECT_NAME, $globalsContent);
		$globalsContent = str_replace('<version>', VERSION, $globalsContent);
		file_put_contents($globals, $globalsContent);

		/*
		 * Create the config files
		 */

		// define the base locations
		$configBase = CLIPATH . 'knife/engine/base/config.base.php';
		$configFrontend = FRONTENDPATH . 'cache/config/config.php';
		$configBackend = BACKENDPATH . 'cache/config/config.php';

		// copy the files
		copy($configBase, $configFrontend);
		copy($configBase, $configBackend);

		/*
		 * If there is still an installer, set the installed.txt file
		 */
		if(is_dir(BASEPATH . 'install/cache'))
		{
			file_put_contents(BASEPATH . 'install/cache/installed.txt', 'This fork is installed with the Fork CLI tool on ' . date('Y-m-d H:i'));
		}
	}

	/**
	 * This goes trough a folder in search of event triggers
	 *
	 * @param array $triggers
	 * @param string $directory
	 * @return array
	 */
	protected function searchDirectoryTriggers(array $triggers, $directory)
	{
		$returnTriggers = $triggers;
		if($handle = opendir($directory))
		{
			while(($file = readdir($handle)) !== false)
			{
				if(is_file($directory . $file))
				{
					$oFile = file($directory . $file);
					foreach($oFile as $line)
					{
						$trimLine = trim($line);
						$trigger = preg_match('/(.*)triggerEvent\((.*)\);/', $trimLine, $pregMatch);
						if($trigger == 1) $returnTriggers[] = $pregMatch[2];
					}
				}
				elseif($file != '.' && $file != '..' && $file != '.svn')
				{
					$returnTriggers = $this->searchDirectoryTriggers($returnTriggers, $directory . $file . '/');
				}
			}
		}
		closedir($handle);

		return $returnTriggers;
	}
}
