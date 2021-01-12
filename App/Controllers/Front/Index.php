<?php

namespace App\Controllers\Front;

class Index extends Front {

	/**
	 * Render homepage with posts.
	 * @return void
	 */
	public function IndexAction () {
		$this->view->title = 'Blog';
		
		$orderDir = $this->GetParam('order', 'a-z', 'desc');
		$posts = \App\Models\Post::GetAll('created', $orderDir);
		$this->view->posts = $posts;

		$defaultOrder = $orderDir == 'desc';
		$this->view->orderLinkText = $defaultOrder
			? 'From oldest'
			: 'From newest';
		$this->view->orderLinkValue = $this->Url(
			'self', ['order' => $defaultOrder ? 'asc' : 'desc']
		);
	}

	/**
	 * Render not found action.
	 * @return void
	 */
	public function NotFoundAction () {
		$this->controllerName = 'front/index';
		$this->ErrorAction();
	}

	/**
	 * Render possible server error action.
	 * @return void
	 */
	public function ErrorAction () {
		$code = $this->response->GetCode();
		if ($code === 200) $code = 404;
		$message = $this->request->GetParam('message', 'a-zA-Z0-9_;, \\/\-\@\:\.');
		$message = preg_replace('#`([^`]*)`#', '<code>$1</code>', $message);
		$message = str_replace("\n", '<br />', $message);
		$this->view->title = "Error $code";
		$this->view->message = $message;
		$this->Render('error');
	}
}
