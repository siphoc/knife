<?php

/*
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

/**
 * This is the edit-action, it will display a form with the item data to edit
 *
 * @author authorname
 */
class Backendmodulenameactionname extends BackendBaseActionEdit
{
	public function execute()
	{
		parent::execute();

		$this->loadData();
		$this->loadForm();
		$this->validateForm();

		$this->parse();
		$this->display();
	}

	private function loadData()
	{
		$this->id = $this->getParameter('id', 'int');
		if(!BackendmodulenameModel::exists($this->id)) $this->redirect(BackendModel::createURLForAction('index') . '&error=non-existing');

		// the data
		$this->record = BackendmodulenameModel::get($this->id);
	}

	private function loadForm()
	{
		// set hidden values
		$rbtVisibleValues[] = array('label' => BL::lbl('Hidden'), 'value' => 'N');
		$rbtVisibleValues[] = array('label' => BL::lbl('Published'), 'value' => 'Y');

		// create form
		$this->frm = new BackendForm('edit');
		$this->frm->addText('title', $this->record['title'], null, 'inputText title', 'inputTextError title');
		$this->frm->addRadiobutton('visible', $rbtVisibleValues, $this->record['visible']);

		// meta
		$this->meta = new BackendMeta($this->frm, $this->record['meta_id'], 'title', true);
	}

	protected function parse()
	{
		// call parent
		parent::parse();

		// assign the item
		$this->tpl->assign('item', $this->record);

		// get url
		$url = BackendModel::getURLForBlock($this->URL->getModule(), 'detail');
		$url404 = BackendModel::getURL(404);
		if($url404 != $url) $this->tpl->assign('detailURL', SITE_URL . $url);
	}

	private function validateForm()
	{
		if($this->frm->isSubmitted())
		{
			// set callback for generating an unique URL
			$this->meta->setUrlCallback('BackendmodulenameModel', 'getURL', array($this->record['id']));

			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// validation
			$this->frm->getField('title')->isFilled(BL::err('FieldIsRequired'));
			$this->meta->validate();

			if($this->frm->isCorrect())
			{
				$item['title'] = $this->frm->getField('title')->getValue();
				$item['meta_id'] = $this->meta->save(true);
				$item['language'] = BL::getWorkingLanguage();
				$item['visible'] = $this->frm->getField('visible')->getValue();

				BackendmodulenameModel::update($item, $this->id);
				$item['id'] = $this->id;

				BackendModel::triggerEvent($this->getModule(), 'after_edit', $item);
				$this->redirect(BackendModel::createURLForAction('index') . '&report=edited&highlight=row-' . $item['id']);
			}
		}
	}
}
