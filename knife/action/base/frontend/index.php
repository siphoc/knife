<?php

/**
 * This is the index-action (default), it will display the overview of subname posts
 *
 * @package		frontend
 * @subpackage	subname
 *
 * @author		authorname
 * @since		versionname
 */
class FrontendclassnameIndex extends FrontendBaseBlock
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// get the data
		$this->getData();

		// parse
		$this->parse();

		// load the template
		$this->loadTemplate();
	}

	/**
	 * Get the data
	 */
	private function getData()
	{
		$this->data = false;
	}

	/**
	 * Parse the data
	 */
	protected function parse()
	{
		// parse the data
		$this->tpl->assign('items', $this->data);
	}
}
