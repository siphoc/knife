<?php

/**
 * This source file is a part of the Knife CLI Tool for Fork CMS.
 * More information can be found on http://www.fork-cms.com
 *
 * @author Jelmer Snoeck <jelmer.snoeck@netlash.com>
 */
class KnifeSettingsGenerator extends KnifeBaseGenerator
{
	/**
	 * This starts the generator.
	 */
	public function init()
	{
		// name given?
		if(!isset($this->arg[0])) throw new Exception('Please specify a setting.');

		$settingData = explode('=', $this->arg[0]);
		$settingName = $settingData[0];

		$settingsFile = $this->readFile(CLIPATH . '/.ftconfig');
		$setting = preg_match('/#' . $settingName . '=(.*);/', $settingsFile, $settingRecord);

		// we have a value, this means we should set it
		if(isset($settingData[1]))
		{
			$settingValue = $settingData[1];

			$settingsFile = preg_replace('/#' . $settingName . '=(.*);/', '#' . $settingName . '=' . $settingValue . ';', $settingsFile);

			$this->makeFile(CLIPATH . '/.ftconfig', $settingsFile);
		}
		// we should read the value
		elseif($setting == 1)
		{
			if($settingRecord[1] == '') echo 'No value set yet. Please provide one.' . "\n";
			else echo $settingRecord[1] . "\n";
			exit;
		}
		// setting could not be found
		else throw new Exception("This setting could not be found.\nValid settings are 'author.name, author.email and author.url'");
	}
}
