<?php

namespace App\Controllers\Front;

class Front extends \App\Controllers\Base
{
	protected $layout = 'front';
	
	public function Init () {
		parent::Init();
		$this->application->SetDefaultControllerName('Front\\Index');
	}

	public function PreDispatch () {
		parent::PreDispatch();
		if ($this->viewEnabled) {
			$this->preDispatchSetUpViewHelpers();
			$this->preDispatchSetUpAssetsFront();
			$this->view->homeLink = $this->Url('front_home');
			$this->view->blogName = 'Blog';
		}
	}

	protected function preDispatchSetUpViewHelpers () {
		/** @var \MvcCore\Ext\Views\Helpers\FormatDateHelper $formateDate */
		$formateDate = $this->view->GetHelper('FormatDate');
		$formateDate
			->SetDefaultIntlDateType(\IntlDateFormatter::MEDIUM)
			->SetDefaultIntlTimeType(\IntlDateFormatter::NONE)
			/** @see http://php.net/strftime */
			->SetDefaultFormatMask('%e. %B %G');
	}

	protected function preDispatchSetUpAssetsFront () {
		$static = self::$staticPath;
		$this->view->Css('varHead')
			->AppendRendered($static . '/css/front.css');
		$this->view->Js('varFoot')
			->Append($static . '/js/Front.js');
	}
}
