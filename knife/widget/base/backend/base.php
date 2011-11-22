<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is a widget
 *
 * @author authorname
 */
class BackendmodulenameWidgetwidgetname extends BackendBaseWidget
{
	/**
	 * The widget data
	 *
	 * @var array
	 */
	protected $record = array();

	/**
	 * Execute the widget
	 */
	public function execute()
	{
		$this->setColumn('middle');
		$this->setPosition(0);
		$this->loadData();
		$this->parse();
		$this->display();
	}

	/**
	 * Load the data
	 */
	private function loadData()
	{
		$this->record = array();
	}

	/**
	 * Parse into template
	 */
	private function parse()
	{
		$this->tpl->assign('widgetwidgetname', $this->record);
	}
}
