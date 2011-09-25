<?php

/**
 * This is the add-action, it will display a form to create a new item
 *
 * @package		backend
 * @subpackage	subname
 *
 * @author		authorname
 * @since		versionname
 */
class Backendmodulenameactionname extends BackendBaseActionAdd
{
	/**
	 * Execute the action
	 *
	 * @return	void
	 */
	public function execute()
	{
		// get parameters
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if($this->id !== null && BackendmodulenameModel::exists($this->id))
		{
			// call parent, this will probably add some general CSS/JS or other required files
			parent::execute();

			// get data
			$this->record = (array) BackendBlogModel::get($this->id);

			// delete item
			BackendBlogModel::delete($this->id);

			// trigger event
			BackendModel::triggerEvent($this->getModule(), 'after_delete', array('id' => $this->id));

			// delete search indexes
			if(is_callable(array('BackendSearchModel', 'removeIndex'))) BackendSearchModel::removeIndex($this->getModule(), $this->id);

			// build redirect URL
			$redirectUrl = BackendModel::createURLForAction('index') . '&report=deleted&var=' . urlencode($this->record['title']);

			// item was deleted, so redirect
			$this->redirect($redirectUrl);
		}

		// something went wrong
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}
}

?>