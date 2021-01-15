<?php

namespace App\Models;

use \MvcCore\Ext\Models\Db\Attrs;

/** 
 * @table users
 */
#[Attrs\Table('users')]
class User 
extends \App\Models\Base
implements \MvcCore\Ext\Auths\Basics\IUser {

	use \MvcCore\Ext\Auths\Basics\User\Features;

	/**
	 * @column email
	 * @keyUnique
	 * @var string|NULL
	 */
	#[Attrs\Column('email'), Attrs\KeyUnique]
	protected $email = NULL;

	/**
	 * @column avatar_url
	 * @var string|NULL
	 */
	#[Attrs\Column('avatar_url')]
	protected $avatarUrl = NULL;
	
	/**
	 * @param string|NULL $email
	 * @return \App\Models\User
	 */
	public function SetEmail ($email) {
		$this->email = $email;
		return $this;
	}

	/**
	 * @return null|string
	 */
	public function GetEmail () {
		return $this->email;
	}

	/**
	 * @param string|NULL $avatarUrl 
	 * @return \App\Models\User
	 */
	public function SetAvatarUrl ($avatarUrl) {
		$this->avatarUrl = $avatarUrl;
		return $this;
	}

	/**
	 * @return null|string
	 */
	public function GetAvatarUrl () {
		return $this->avatarUrl;
	}


	/**
	 * Get user model instance from database or any other users list
	 * resource by submitted and cleaned `$userName` field value.
	 * @param string $userName Submitted and cleaned username. Characters `' " ` < > \ = ^ | & ~` are automatically encoded to html entities by default `\MvcCore\Ext\Auths\Basic` sign in form.
	 * @return \App\Models\User|NULL
	 */
	public static function GetByUserName ($userName) {
		return self::GetConnection()
			->Prepare([
				"SELECT *						",
				"FROM users u					",
				"WHERE u.user_name = :user_name;",
			])
			->FetchOne([':user_name' => $userName])
			->ToInstance(
				get_called_class(),
				self::PROPS_INHERIT |
				self::PROPS_PROTECTED |
				self::PROPS_CONVERT_UNDERSCORES_TO_CAMELCASE | 
				self::PROPS_INITIAL_VALUES
			);
	}

	/**
	 * @param string $email
	 * @return \App\Models\User|NULL
	 */
	public static function GetByUserEmail ($email) {
		return self::GetConnection()
			->Prepare([
				"SELECT *				",
				"FROM users u			",
				"WHERE u.email = :email;",
			])
			->FetchOne([':email' => $email])
			->ToInstance(
				get_called_class(),
				self::PROPS_INHERIT |
				self::PROPS_PROTECTED |
				self::PROPS_CONVERT_UNDERSCORES_TO_CAMELCASE | 
				self::PROPS_INITIAL_VALUES
			);
	}
}