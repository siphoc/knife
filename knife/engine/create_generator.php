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

			$returnString.= "\t\t" . '<event application="' . $location . '" name="' . $eventName . '"><![CDATA[Triggered when .]]></event>' . "\n";
		}

		return $returnString;
	}

	/**
	 * This will generate the info.xml file from the module files. It will go trough
	 * them and search for triggers.
	 *
	 * BackendModel::triggerEvent($this->getModule(), 'after_add', array('item' => $item));
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
