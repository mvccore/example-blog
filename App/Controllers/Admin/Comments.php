<?php

namespace App\Controllers\Admin;

use \App\Models,
	\MvcCore\Ext\Form;

class Comments extends Index
{
	/** @var \App\Models\Comment */
	protected $comment = NULL;

	/**
	 * Pre execute every action in this controller. This method
	 * is template method - so it's necessary to call parent method first.
	 * @return void
	 */
	public function PreDispatch () {
		parent::PreDispatch();
		// if there is any 'id' param in `$_GET` or `$_POST`,
		// try to load post model instance from database
		$id = $this->GetParam("id", "0-9", NULL, 'int');
		if ($id !== NULL)
			$this->comment = \App\Models\Comment::GetById($id, FALSE);
		if (!$this->comment && $this->actionName !== 'index')
			$this->RenderNotFound();
	}

	/**
	 * Render comments.
	 * @return void
	 */
	public function IndexAction () {
		$this->view->title = 'Comments';

		$orderCol = $this->GetParam('order', 'a-z_', 'id');
		$orderDir = $this->GetParam('dir', 'a-z', 'desc');
		$filterCol = $this->GetParam('filter', '_a-zA-Z', NULL);
		$filterVal = $this->GetParam('value', '\-\._a-zA-Z0-9', NULL);
		$comments = \App\Models\Comment::GetAll(
			$orderCol, $orderDir, $filterCol, $filterVal
		);
		$this->view->comments = $comments;

		/** @var \MvcCore\Ext\Form $abstractForm */
		list($this->view->csrfName, $this->view->csrfValue)
			= $this->getVirtualActivationForm()->SetUpCsrf();
		$this->view->Js('varFoot')
			->Prepend($this->application->GetPathStatic() . '/js/List.js');

		$defaultOrder = $orderDir == 'desc';
		$this->view->order = $orderCol;
		$this->view->dir = $orderDir == 'desc' ? 'asc' : 'desc';
		$this->view->orderLinkText = $defaultOrder
			? 'From oldest'
			: 'From newest';
		$this->view->orderLinkValue = $this->Url(
			'self', ['order' => $defaultOrder ? 'asc' : 'desc']
		);
	}

	/**
	 * @return void
	 */
	public function DetailAction () {
		$this->view->title = 'View comment';
		$this->view->comment = $this->comment;
	}

	/**
	 * @return void
	 */
	public function ActivationAction () {
		$form = $this->getVirtualActivationForm();
		$form->SubmitCsrfTokens($_POST);
		if ($form->GetResult() !== \MvcCore\Ext\IForm::RESULT_ERRORS) {
			$activate = $this->request->GetParam('activate', 'a-zA-Z');
			$deactivate = $this->request->GetParam('deactivate', 'a-zA-Z');
			if ($activate !== NULL) {
				$this->comment->Active = 1;
				$this->comment->Save();
			} else if ($deactivate !== NULL) {
				$this->comment->Active = 0;
				$this->comment->Save();
			}
		}
		self::Redirect($this->Url(':Index'));
	}

	/**
	 * Create empty form, where to store and manage CSRF
	 * tokens for active/deactive links in posts list.
	 * @return \MvcCore\Ext\Form
	 */
	protected function getVirtualActivationForm () {
		return (new Form($this))
			->SetId('activation')
			// set error url, where to redirect if CSRF
			// are wrong, see `\App\Controllers\Base::Init();`
			->SetErrorUrl(
				$this->Url(':Index', ['absolute' => TRUE])
			);
	}
}
