<?php

/**
 * This is an ajax handler
 *
 * @package		backend
 * @subpackage	subname
 *
 * @author		authorname
 * @since		versionname
 */
class BackendmodulenameAjaxactionname extends BackendBaseAJAXAction
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
