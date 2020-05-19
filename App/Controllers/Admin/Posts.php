<?php

namespace App\Controllers\Admin;

use \App\Models,
	\MvcCore\Ext\Forms\IForm;

class Posts extends Index {

	/** @var \App\Models\Post */
	protected $post = NULL;

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
			$this->post = Models\Post::GetById(intval($id));
		if (!$this->post && $this->actionName == 'edit')
			$this->renderNotFound();
	}

	/**
	 * Render posts.
	 * @return void
	 */
	public function IndexAction () {
		$this->view->title = 'Posts';

		$orderCol = $this->GetParam('order', 'a-z', 'id');
		$orderDir = $this->GetParam('dir', 'a-z', 'desc');
		$posts = \App\Models\Post::GetAll($orderCol, $orderDir);
		$this->view->posts = $posts;

		/** @var $abstractForm \MvcCore\Ext\Form */
		list($this->view->csrfName, $this->view->csrfValue)
			= $this->getVirtualDeleteForm()->SetUpCsrf();
		$this->view->Js('varFoot')
			->Prepend(self::$staticPath . '/js/List.js');

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
	 * Create form for new post without hidden id input.
	 * @return void
	 */
	public function CreateAction () {
		$this->view->title = 'New post';
		$this->view->detailForm = $this->getCreateEditForm();
	}

	/**
	 * Load previously saved post data,
	 * create edit form with hidden id input
	 * and set form defaults with post values.
	 * @return void
	 */
	public function EditAction () {
		$this->view->title = 'Edit post - ' . $this->post->Title;
		$this->view->detailForm = $this->getCreateEditForm();
	}

	/**
	 * Handle create and edit action form submit.
	 * @return void
	 */
	public function SubmitAction () {
		$detailForm = $this->getCreateEditForm();
		if (!$this->post) {
			$this->post = new Models\Post();
			$detailForm->SetErrorUrl($this->Url(':Create', ['absolute' => TRUE]));
		} else {
			$detailForm->SetErrorUrl($this->Url(':Edit', ['id' => $this->post->Id, 'absolute' => TRUE]));
		}
		list($result) = $detailForm->Submit();
		if ($result === IForm::RESULT_SUCCESS)
			$detailForm->ClearSession();
		$detailForm->SubmittedRedirect();
	}

	/**
	 * Delete post by sent id param, if sent CSRF tokens
	 * are the same as CSRF tokens in session (tokens are managed
	 * by empty virtual delete form initialized once, not for all post rows).
	 * @return void
	 */
	public function DeleteAction () {
		$form = $this->getVirtualDeleteForm();
		$form->SubmitCsrfTokens($_POST);
		if ($form->GetResult() !== IForm::RESULT_ERRORS)
			$this->post->Delete();
		self::Redirect($this->Url(':Index'));
	}

	/**
	 * Create form instance to create new or edit existing post.
	 * @return \App\Forms\CreateEditPost
	 */
	protected function getCreateEditForm ($editForm = TRUE) {
		$form = new \App\Forms\CreateEditPost($this);
		$form
			->SetPost($this->post)
			->SetAction($this->Url(':Submit'))
			->SetSuccessUrl($this->Url(':Index', ['absolute' => TRUE]));
		return $form;
	}

	/**
	 * Create empty form, where to store and manage CSRF
	 * tokens for delete links in posts list.
	 * @return \MvcCore\Ext\Form|\MvcCore\Ext\Forms\IForm
	 */
	protected function getVirtualDeleteForm () {
		return (new \MvcCore\Ext\Form($this))
			->SetId('delete')
			// set error url, where to redirect if CSRF
			// are wrong, see `\App\Controllers\Base::Init();`
			->SetErrorUrl(
				$this->Url(':Index', ['absolute' => TRUE])
			);
	}
}
