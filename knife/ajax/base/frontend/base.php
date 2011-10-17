<?php

/**
 * This is an ajax handler
 *
 * @package		frontend
 * @subpackage	subname
 *
 * @author		authorname
 * @since		versionname
 */
class FrontendmodulenameAjaxactionname extends FrontendBaseAJAXAction
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// get parameters
		$itemId = (int) trim(SpoonFilter::getPostValue('id', null, '', 'int'));

		// output
		$this->output(self::OK, $return, FL::msg('Success'));
	}
}
