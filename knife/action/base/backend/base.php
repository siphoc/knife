<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the actionname action, it will display a form to create a new item
 *
 * @author authorname
 */
class Backendmodulenameactionname extends BackendBaseAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		$this->parse();
		$this->display();
	}

	/**
	 * Parse the page
	 */
	protected function parse()
	{

	}
}
