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
	 *
	 * @return	void
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
	 *
	 * @return	void
	 */
	private function loadDataGrid()
	{
		$this->dataGrid = new BackendDataGridDB(QUERY, PARAMETERS);
	}


	/**
	 * Parse
	 *
	 * @return	void
	 */
	protected function parse()
	{
		// parse the datagrid for the drafts
		$this->tpl->assign('dataGrid', ($this->dataGrid->getNumResults() != 0) ? $this->dataGrid->getContent() : false);
	}
}

?>