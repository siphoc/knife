<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * Installer for the subname module
 *
 * @author		authorname
 */
class classnameInstall extends ModuleInstaller
{
	public function execute()
	{
		$this->importSQL(dirname(__FILE__) . '/data/install.sql');
		$this->addModule('subname', 'The subname module.');
		$this->importLocale(dirname(__FILE__) . '/data/locale.xml');

		$this->setModuleRights(1, 'subname');

		$this->setActionRights(1, 'subname', 'index');

		// add extra's
		$subnameID = $this->insertExtra('subname', 'block', 'classname', null, null, 'N', 1000);

		$navigationModulesId = $this->setNavigation(null, 'Modules');
		$navigationclassnameId = $this->setNavigation($navigationModulesId, 'classname', 'subname/index');
	}
}
