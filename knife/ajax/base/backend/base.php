<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is an ajax handler
 *
 * @author authorname
 */
class BackendmodulenameAjaxactionname extends BackendBaseAJAXAction
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		// get parameters
		$itemId = (int) trim(SpoonFilter::getPostValue('id', null, '', 'int'));

		// output
		$this->output(self::OK, $return, FL::msg('Success'));
	}
}
