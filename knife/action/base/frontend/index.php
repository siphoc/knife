<?php

/**
 * This is the actionname-action (default), it will display the overview of subname posts
 *
 * @package		frontend
 * @subpackage	subname
 *
 * @author		authorname
 * @since		versionname
 */
class Frontendmodulenameactionname extends FrontendBaseBlock
{
	/**
	 * The record
	 *
	 * @var	array
	 */
	private $record;

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
		$this->record = false;
	}

	/**
	 * Parse the data
	 */
	protected function parse()
	{
		// parse the data
		$this->tpl->assign('items', $this->record);
	}
}
