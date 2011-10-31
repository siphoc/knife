<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the actionname-action (default), it will display the overview of subname posts
 *
 * @author authorname
 */
class Frontendmodulenameactionname extends FrontendBaseBlock
{
	/**
	 * The record data
	 *
	 * @var	array
	 */
	private $record;

	/**
	 * Execute the action
	 */
	public function execute()
	{
		parent::execute();

		$this->loadData();

		$this->parse();
		$this->loadTemplate();
	}

	private function loadData()
	{
		$this->record = false;
	}

	protected function parse()
	{
		$this->tpl->assign('items', $this->record);
	}
}
