<?php

namespace App\Controllers\Front;

class Front extends \App\Controllers\Base
{
	protected $layout = 'front';

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
		/** @var $formateDate \MvcCore\Ext\Views\Helpers\FormatDateHelper */
		$formateDate = $this->view->GetHelper('FormatDate');
		$formateDate
			->SetIntlDefaultDateFormatter(\IntlDateFormatter::MEDIUM)
			->SetIntlDefaultTimeFormatter(\IntlDateFormatter::NONE)
			/** @see http://php.net/strftime */
			->SetStrftimeFormatMask('%e. %B %G');
	}

	protected function preDispatchSetUpAssetsFront () {
		$static = self::$staticPath;
		$this->view->Css('varHead')
			->AppendRendered($static . '/css/front.css');
		$this->view->Js('varFoot')
			->Append($static . '/js/Front.js');
	}
}
