<?php

/**
 * This is a widget
 *
 * @package		frontend
 * @subpackage	subname
 *
 * @author		authorname
 * @since		versionname
 */
class FrontendmodulenameWidgetwidgetname extends FrontendBaseWidget
{
	/**
	 * Execute the extra
	 */
	public function execute()
	{
		// call parent
		parent::execute();

		// get the data
		$this->loadData();

		// load template
		$this->loadTemplate();

		// parse
		$this->parse();
	}

	/**
	 * Load the data
	 */
	private function loadData()
	{
		$this->data = false;
	}

	/**
	 * Parse the data
	 */
	protected function parse()
	{
		$this->tpl->assign('widgetname', $this->data);
	}
}
