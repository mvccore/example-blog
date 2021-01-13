<?php

namespace App\Forms;

use \MvcCore\Ext\Forms,
	\MvcCore\Ext\Forms\Fields;

class AddComment extends \MvcCore\Ext\Form
{
	protected $cssClasses = ['theme', 'comment'];

	protected $method = \MvcCore\IRequest::METHOD_POST;

	protected $idPost = NULL;
	
	protected $csrfEnabled = TRUE; // training purposes, true by default

	/**
	 * @param int $idPost 
	 * @return \App\Forms\AddComment|\MvcCore\Ext\Form
	 */
	public function SetIdPost ($idPost) {
		$this->idPost = $idPost;
		$this->SetId('add_cmnt_post_' . $idPost);
		return $this;
	}

	public function Init ($submit = FALSE) {
		parent::Init($submit);

		$title = (new Fields\Text)
			//->SetValidators([]) // training purposes
			->SetRequired()
			->SetPlaceHolder('Comment title')
			->SetName('title');

		$content = (new Fields\Textarea)
			//->SetValidators([]) // training purposes
			->SetRequired()
			->SetPlaceHolder('Comment content')
			->SetName('content');

		$send = (new Fields\SubmitButton)
			->SetName('send');

		$this->AddFields($title, $content, $send);
		return $this;
	}

	public function Submit (array & $rawRequestParams = []) {
		parent::Submit($rawRequestParams);
		if ($this->result == self::RESULT_SUCCESS) {
			try {
				$data = (object) $this->values;
				$newComment = new \App\Models\Comment;

				$newComment->IdPost = $this->idPost;
				$newComment->IdUser = $this->user->GetId();
				$newComment->Title = $data->title;
				$newComment->Content = $data->content;

				$newComment->Save(TRUE);

			} catch (\Throwable $e) {
				\MvcCore\Debug::Log($e);
				$this->AddError('Error when saving new comment. See more in application log.');
			}
		}
		return [
			$this->result,
			$this->values,
			$this->errors,
		];
	}
}