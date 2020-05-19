<?php

namespace App\Controllers\Api\Services;

class Posts extends \App\Controllers\Api\Service
{
	public function getTitleByPath ($path) {
		$post = \App\Models\Post::GetByPath($path);
		if (!$post) return NULL;
		return $post->Title;
	}
}