<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the elete-action, it delete an item
 *
 * @author authorname
 */
class Backendmodulenameactionname extends BackendBaseActionDelete
{
	/**
	 * Execute the action
	 */
	public function execute()
	{
		$this->id = $this->getParameter('id', 'int');

		// does the item exist
		if($this->id !== null && BackendmodulenameModel::exists($this->id))
		{
			parent::execute();
			$this->record = (array) BackendmodulenameModel::get($this->id);

			BackendmodulenameModel::delete($this->id);

			BackendModel::triggerEvent($this->getModule(), 'after_delete', array('id' => $this->id));

			if(is_callable(array('BackendSearchModel', 'removeIndex'))) BackendSearchModel::removeIndex($this->getModule(), $this->id);
			$this->redirect(BackendModel::createURLForAction('index') . '&report=deleted&var=' . urlencode($this->record['title']));
		}
		else $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');
	}
}
