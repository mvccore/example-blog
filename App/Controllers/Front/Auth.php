<?php

namespace App\Controllers\Front;

use \MvcCore\Ext\Forms;

class Auth extends \App\Controllers\Front\Front
{
	public function Init() {
		parent::Init();
		if ($this->user !== NULL) {
			if ($this->user->IsAdmin()) {
				self::Redirect(
					$this->Url('admin_home'),
					\MvcCore\IResponse::SEE_OTHER,
					'known admin'
				);
			} else {
				self::Redirect(
					$this->Url('front_home'),
					\MvcCore\IResponse::SEE_OTHER,
					'known user'
				);
			}
			$this->Terminate();
		}
	}

	/**
	 * @return void
	 */
	public function SignInAction () {
		$this->view->title = 'Login';
		$this->view->signInForm = \MvcCore\Ext\Auths\Basic::GetInstance()
			->GetSignInForm()
			->AddCssClasses('theme')
			->SetValues([// set signed in url to blog posts list by default:
				'successUrl' => $this->Url('front_home', ['absolute' => TRUE]),
			]);
		$this->view->registrationLink = $this->Url('front_register');
	}

	public function RegisterAction () {
		$this->view->title = 'Registration';
		$this->view->registerForm = $this->getRegistrationForm();
	}

	public function SubmitAction () {
		$form = $this->getRegistrationForm();
		list ($result, $values, $errors) = $form->Submit();
		if ($result === Forms\IForm::RESULT_SUCCESS)
			$form->ClearSession();
		$form->SubmittedRedirect();
	}

	protected function getRegistrationForm () {
		return (new \App\Forms\UserRegistration($this))
			->SetAction($this->Url(':Submit', ['absolute' => TRUE]))
			->SetErrorUrl($this->Url(':Register', ['absolute' => TRUE]))
			->SetSuccessUrl($this->Url('front_home', ['absolute' => TRUE]));
	}
}