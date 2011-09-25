<?php

/**
 * Installer for the subname module
 *
 * @package		installer
 * @subpackage	subname
 *
 * @author		authorname
 * @since		versionname
 */
class classnameInstall extends ModuleInstaller
{
	/**
	 * Execute the installer
	 *
	 * @return	void
	 */
	public function execute()
	{
		// load install.sql
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');

		// add 'temname' as a module
		$this->addModule('subname', 'The subname module.');

		// import locale
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		// module rights
		$this->setModuleRights(1, 'subname');

		// set action rights
		$this->setActionRights(1, 'subname', 'index');

		// add extra's
		$subnameID = $this->insertExtra('subname', 'block', 'classname', null, null, 'N', 1000);
	}
}

?>
