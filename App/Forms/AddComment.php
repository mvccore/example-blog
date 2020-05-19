<?php

namespace App\Forms;

use \MvcCore\Ext\Forms,
	\MvcCore\Ext\Forms\Fields;

class AddComment extends \MvcCore\Ext\Form
{
	protected $cssClasses = ['theme', 'comment'];

	protected $method = \MvcCore\IRequest::METHOD_POST;

	protected $idPost = NULL;
	
	//protected $csrfEnabled = FALSE;

	public function SetIdPost ($idPost) {
		$this->idPost = $idPost;
		return $this;
	}

	public function Init () {
		parent::Init();

		$title = (new Fields\Text)
			//->SetValidators([])
			->SetRequired()
			->SetPlaceHolder('Comment title')
			->SetName('title');

		$content = (new Fields\Textarea)
			//->SetValidators([])
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
		if ($this->result == Forms\IForm::RESULT_SUCCESS) {
			try {
				$data = (object) $this->values;
				\App\Models\Comment::AddNew(
					$this->idPost,
					$this->user->GetId(),
					$data->title,
					$data->content
				);
			} catch (\Exception $e) {
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