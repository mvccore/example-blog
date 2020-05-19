<?php

namespace App\Controllers\Api;

class Soap extends \MvcCore\Controller
{
	protected $viewEnabled = FALSE;

	public function Init () {
		parent::Init();
		\Tracy\Debugger::enable(true); // disable tracy output
		$this->response
			->SetDisabledHeaders(['Content-Encoding'])
			->SetHeader('Content-Type', 'text/xml; charset=utf-8');
		$wsdl = $this->request->HasParam('wsdl');
		if ($wsdl) {
			$xmlStr = file_get_contents($this->getWsdlFullPath());
			$xmlStr = str_replace(
				['%LOCATION%', '%SERVICE%'], 
				[$this->request->GetRequestUrl(), $this->actionName], 
				$xmlStr
			);
			$this->response->SetBody($xmlStr);
			$this->Terminate();
		}
	}

	protected function getWsdlFullPath () {
		$wsdlName = $this->actionName;
		$appRoot = $this->request->GetAppRoot();
		return $appRoot . "/App/{$wsdlName}.wsdl";
	}

	public function PostsAction () {
		$server = new \SoapServer($this->getWsdlFullPath());
		$server->setClass(\App\Controllers\Api\Services\Posts::class);
		$server->handle();
	}

	public function CommentsAction () {
		$server = new \SoapServer($this->getWsdlFullPath());
		$server->setClass(\App\Controllers\Api\Services\Comments::class);
		$server->handle();
	}
}
