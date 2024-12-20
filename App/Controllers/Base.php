<?php

namespace App\Controllers;

class Base extends \MvcCore\Controller
{
	/**
	 * Authenticated user instance is automatically assigned
	 * by authentication extension before `Controller::Init();`.
	 * @var \MvcCore\Ext\Auths\Basics\IUser
	 */
	protected $user = NULL;
	
	public function Init() {
		parent::Init();
		// when any CSRF token is outdated or not the same - sign out user by default
		\MvcCore\Ext\Form::AddCsrfErrorHandler(function (\MvcCore\Ext\Form $form, $errorMsg) {
			\MvcCore\Ext\Auths\Basics\User::LogOut();
			self::Redirect($this->Url(
				'front_login',
				['absolute' => TRUE, 'sourceUrl'	=> rawurlencode($form->GetErrorUrl())]
			));
		});
	}

	public function PreDispatch () {
		parent::PreDispatch();
		if ($this->viewEnabled) {
			$this->preDispatchSetUpAssetsBase();
			$this->view->user = $this->user;
			if ($this->user) {
				// set sign-out form into view, set signed-out url to homepage:
				$this->view->signOutForm = \MvcCore\Ext\Auths\Basic::GetInstance()
					->GetSignOutForm()
					->SetValues([
						'successUrl' => $this->Url('front_login', ['absolute' => TRUE])
					]);
			} else if ($this->controllerName != 'front/auth') {
				$this->view->signInLink = $this->Url('front_login');
			}
			$this->view->basePath = $this->request->GetBasePath();
			$this->view->currentRouteCssClass = str_replace(
				':', '-', strtolower(
					$this->router->GetCurrentRoute()->GetName()
				)
			);
		}
	}

	protected function preDispatchSetUpAssetsBase () {
		\MvcCore\Ext\Views\Helpers\Assets::SetGlobalOptions(
			(array) \MvcCore\Config::GetConfigSystem()->assets
		);
		$static = $this->application->GetPathStatic();
		$this->view->Css('fixedHead')
			->Append($static . '/css/components/resets.css')
			->Append($static . '/css/components/old-browsers-warning.css')
			->AppendRendered($static . '/css/components/fonts.css')
			->AppendRendered($static . '/css/components/forms-and-controls.css')
			->AppendRendered($static . '/css/components/content-buttons.css')
			->AppendRendered($static . '/css/components/content-tables.css')
			->AppendRendered($static . '/css/layout.css')
			->AppendRendered($static . '/css/common-content.css');
		$this->view->Js('fixedHead')
			->Append($static . '/js/libs/class.min.js')
			->Append($static . '/js/libs/ajax.min.js')
			->Append($static . '/js/libs/Module.js');
	}
}
