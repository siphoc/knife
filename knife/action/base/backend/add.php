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
	 */
	public function execute()
	{
		// call parent, this will probably add some general CSS/JS or other required files
		parent::execute();

		// load the form
		$this->loadForm();

		// validate the form
		$this->validateForm();

		// parse
		$this->parse();

		// display the page
		$this->display();
	}

	/**
	 * Load the form
	 */
	private function loadForm()
	{
		// create form
		$this->frm = new BackendForm('add');

		// set hidden values
		$rbtVisibleValues[] = array('label' => BL::lbl('Hidden', $this->URL->getModule()), 'value' => 'N');
		$rbtVisibleValues[] = array('label' => BL::lbl('Published'), 'value' => 'Y');

		// create elements
		$this->frm->addText('title', null, null, 'inputText title', 'inputTextError title');
		$this->frm->addRadiobutton('visible', $rbtVisibleValues, 'Y');

		// meta
		$this->meta = new BackendMeta($this->frm, null, 'title', true);
	}

	/**
	 * Parse the form
	 */
	protected function parse()
	{
		// call parent
		parent::parse();

		// parse the form
		$this->frm->parse($this->tpl);

		// get url
		$url = BackendModel::getURLForBlock($this->URL->getModule(), 'detail');
		$url404 = BackendModel::getURL(404);

		// parse additional variables
		if($url404 != $url) $this->tpl->assign('detailURL', SITE_URL . $url);
	}

	/**
	 * Validate the form
	 */
	private function validateForm()
	{
		// is the form submitted?
		if($this->frm->isSubmitted())
		{
			// cleanup the submitted fields, ignore fields that were added by hackers
			$this->frm->cleanupFields();

			// validation
			$this->frm->getField('title')->isFilled(BL::err('FieldIsRequired'));

			// validate meta
			$this->meta->validate();

			// no errors?
			if($this->frm->isCorrect())
			{
				// get the values
				$item['title'] = $this->frm->getField('title')->getValue();
				$item['meta_id'] = $this->meta->save();
				$item['language'] = BL::getWorkingLanguage();
				$item['visible'] = $this->frm->getField('visible')->getValue();

				// insert
				$item['id'] = BackendmodulenameModel::insert($item);

				// trigger event
				BackendModel::triggerEvent($this->getModule(), 'after_add', $item);

				// everything is saved, so redirect to the index
				$this->redirect(BackendModel::createURLForAction('index') . '&report=added');
			}
		}
	}
}
