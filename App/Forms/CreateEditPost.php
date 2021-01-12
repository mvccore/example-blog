<?php

namespace App\Forms;

use \MvcCore\Ext\Forms,
	\MvcCore\Ext\Forms\Fields;

class CreateEditPost extends \MvcCore\Ext\Form
{
	protected $id = 'post_detail';

	protected $cssClasses = ['theme', 'post'];

	protected $method = \MvcCore\IRequest::METHOD_POST;

	protected $efaultFieldsRenderMode = \MvcCore\Ext\Form::FIELD_RENDER_MODE_LABEL_AROUND;

	protected $edit = NULL;
	
	//protected $csrfEnabled = FALSE;

	public function SetPost ($post) {
		$this->post = $post;
		return $this;
	}

	public function Init ($submit = FALSE) {
		parent::Init($submit);

		$title = (new Fields\Text)
			->SetName('title')
			->SetLabel('Title:')
			->SetMaxLength(200)
			->SetRequired()
			->SetAutocomplete('off');
		
		$path = (new Fields\Text)
			->SetName('path')
			->SetLabel('URL path:')
			->SetMaxLength(200)
			->SetAutocomplete('off');

		$perex = (new Fields\Textarea)
			->SetName('perex')
			->SetLabel('Perex:')
			->SetMaxLength(100)
			->SetAutocomplete('off');

		$content = (new Fields\Textarea)
			->SetName('content')
			->SetLabel('Content:')
			->SetRequired()
			->SetAutocomplete('off');

		$send = (new Fields\SubmitButton)
			->SetName('send')
			->SetCssClasses('btn btn-large')
			->SetValue('Save');
		
		$this->AddFields($title, $path, $perex, $content, $send);

		if ($this->post !== NULL) {

			$id = (new Fields\Hidden)
				->SetName('id')
				->AddValidators('IntNumber');
			$this->AddField($id);
			
			if (!$submit && !$this->GetValues()) {
				$values = $this->post->GetValues(
					\App\Models\Post::PROPS_PUBLIC |
					\App\Models\Post::PROPS_CONVERT_PASCALCASE_TO_CAMELCASE, 
					TRUE
				);
				$this->SetValues($values);
			}
		}

		return $this;
	}

	public function Submit (array & $rawRequestParams = []) {
		parent::Submit($rawRequestParams);
		if ($this->post === NULL) {
			$this->post = new \App\Models\Post();
			$this->post->Created = new \DateTime('now');
		}
		if ($this->result === \MvcCore\Ext\IForm::RESULT_SUCCESS) {
			try {
				$data = $this->values;
				if (!$data['path']) 
					$data['path'] = preg_replace(
						"#[\-]+#", '-', preg_replace("#[^-_a-zA-Z0-9\-]#", '-', $data['title'])
					);

				$this->post->SetValues(
					$data, 
					\App\Models\Post::PROPS_PUBLIC | 
					\App\Models\Post::PROPS_CONVERT_CAMELCASE_TO_PASCALCASE
				);
				$this->post->Updated = new \DateTime('now');
				
				$this->post->Save();

			} catch (\Exception $e) {
				\MvcCore\Debug::Log($e);
				$this->AddError('Error when saving blog post. See more in application log.');
			}
		}
		return [
			$this->result,
			$this->values,
			$this->errors,
		];
	}
}