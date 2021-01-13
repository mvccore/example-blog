<?php

namespace App\Controllers\Front;

use \MvcCore\Ext\Form;

class Post extends Index {

	/** @var \App\Models\Post */
	protected $post = NULL;

	public function PreDispatch () {
		parent::PreDispatch();
		$path = $this->GetParam('path', '-_a-zA-Z0-9');
		$this->post = \App\Models\Post::GetByPath($path);
		if (!$this->post) return $this->NotFoundAction();

		if (!$this->viewEnabled) return;
		$this->view->post = $this->post;

		$formateDateTime = (new \MvcCore\Ext\Views\Helpers\FormatDateHelper)
			->SetIntlDefaultDateFormatter(\IntlDateFormatter::MEDIUM)
			->SetIntlDefaultTimeFormatter(\IntlDateFormatter::MEDIUM)
			/** @see http://php.net/strftime */
			->SetStrftimeFormatMask('%e. %B %G, %H:%M:%S');
		$formatDateTimeHelper = function ($date) use ($formateDateTime) {
			return call_user_func_array([$formateDateTime, 'FormatDate'], func_get_args());
		};
		$this->view->SetHelper('FormatDateTime', $formatDateTimeHelper);
	}
	/**
	 * Render post detail with comments.
	 * @return void
	 */
	public function IndexAction () {
		$this->view->title = $this->post->Title;
		$comments = $this->post->GetComments();
		$appBaseUrl = $this->request->GetBaseUrl();
		foreach ($comments as $comment) 
			if (
				$comment->AvatarUrl &&
				mb_substr($comment->AvatarUrl, 0, 4) !== 'http'
			) $comment->AvatarUrl = $appBaseUrl
				. $comment->AvatarUrl;
		$this->view->comments = $comments;

		if ($this->user) {
			$this->view->commentForm = $this->getCommentForm();
		} else {
			$this->view->registerLink = $this->Url('front_register');
			$this->view->loginLink = $this->Url('front_login');
		}
	}

	public function CommentSubmitAction () {
		$form = $this->getCommentForm();
		list($result/*, $values, $errors*/) = $form->Submit();
		if ($result === Form::RESULT_SUCCESS)
			$form->ClearSession();
		$form->SubmittedRedirect();
	}

	/**
	 * @return \App\Forms\AddComment
	 */
	protected function getCommentForm () {
		$params = ['path' => $this->post->Path, 'absolute' => TRUE];
		$selfUrl = $this->Url('front_post', $params);
		$form = new \App\Forms\AddComment($this);
		$form
			->SetIdPost($this->post->Id)
			->SetAction($this->Url(':CommentSubmit', $params))
			->SetErrorUrl($selfUrl)
			->SetSuccessUrl($selfUrl);
		return $form;
	}

}
