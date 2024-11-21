<?php

namespace App\Controllers\Admin;

class Admin extends \App\Controllers\Base
{
	protected $layout = 'admin';

	/**
	 * Initialize this controller, before pre-dispatching and before every action
	 * executing in this controller. This method is template method - so
	 * it's necessary to call parent method first.
	 * @return void
	 */
	public function Init () {
		parent::Init();
		$this->application->SetDefaultControllerName('Admin\\Index');
		// if user is not authorized, redirect to proper location and exit
		if (!$this->user || ($this->user && !$this->user->IsAdmin())) {
			// if post, get safe value from where the form has been submitted
			$sourceUrl = (
				$this->request->GetMethod() === \MvcCore\Request::METHOD_POST &&
				parse_url($this->request->GetReferer(), PHP_URL_HOST) === $this->request->GetHostName()
			)
				? $this->request->GetReferer()
				: $this->request->GetFullUrl();
			self::Redirect($this->Url(
				'front_login', ['sourceUrl' => rawurlencode($sourceUrl)]
			));
		}
	}

	public function PreDispatch () {
		parent::PreDispatch();
		if ($this->viewEnabled) {
			$this->preDispatchSetUpViewHelpers();
			$this->preDispatchSetUpAssetsAdmin();
		}
	}
	
	protected function preDispatchSetUpViewHelpers () {
		/** @var \MvcCore\Ext\Views\Helpers\FormatDateHelper $formateDate */
		$formateDate = $this->view->GetHelper('FormatDate');
		$formateDate
			->SetDefaultIntlDateType(\IntlDateFormatter::SHORT)
			->SetDefaultIntlTimeType(\IntlDateFormatter::SHORT);
	}

	protected function preDispatchSetUpAssetsAdmin () {
		$static = $this->application->GetPathStatic();
		$this->view->Css('varHead')
			->AppendRendered($static . '/css/admin.css');
		$this->view->Js('varFoot')
			->Append($static . '/js/Admin.js');
	}
}
