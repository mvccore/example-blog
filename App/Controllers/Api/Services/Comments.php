<?php

namespace App\Controllers\Api\Services;

class Comments extends \App\Controllers\Api\Service
{
	public function hello ($name, $surname) {
		return "{$name}_{$surname}_1";
	}
}