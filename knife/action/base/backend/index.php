<?php

/**
 * This is the index-action (default), it will display the overview of subname posts
 *
 * @package		backend
 * @subpackage	subname
 *
 * @author		authorname
 * @since		versionname
 */
class BackendclassnameIndex extends BackendBaseActionIndex
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load the dataGrid
		$this->loadDataGrid();

		// parse
		$this->parse();

		// display the page
		$this->display();
	}

	/**
	 * Load the dataGrid
	 */
	private function loadDataGrid()
	{
		// create the dataGrid
		$this->dataGrid = new BackendDataGridDB(QUERY, PARAMETERS);

		// add edit column
		$this->dataGrid->addColumn('edit', null, BL::lbl('Edit'), BackendModel::createURLForAction('edit') . '&amp;id=[id]', BL::lbl('Edit'));
	}

	/**
	 * Parse
	 */
	protected function parse()
	{
		// parse the dataGrid
		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);
	}
}
