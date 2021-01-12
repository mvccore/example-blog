<?php

namespace App\Models;

use \MvcCore\Ext\Database\{
	Statement, 
	Attributes as Attrs
};

/** 
 * @connection my57
 */
#[Attrs\Connection('my57')]
class User extends \MvcCore\Ext\Auths\Basics\User {

	use \MvcCore\Ext\Database\Model\Features;

	/**
	 * @var string|NULL
	 * @column avatar_url
	 */
	protected $avatarUrl = NULL;

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
	 * @param string $fullName
	 * @param string $userName
	 * @param string $email
	 * @param string $password
	 * @param string $avatarUrl
	 * @return int
	 */
	public static function Register (
		$fullName, $userName, $email, $password, $avatarUrl
	) {
		$newId = NULL;
		$db = self::GetConnection();
		try {

			$db->beginTransaction();

			$sql = $sql = implode("\n", [
				"INSERT INTO users (						",
				"	active, admin, user_name, full_name, 	",
				"	email, password_hash, avatar_url		",
				") VALUES (									",
				"	1, 0, :user_name, :full_name, 			",
				"	:email, :password_hash, :avatar_url		",
				");											",
			]);
			$insert = self::GetConnection()->prepare($sql);
			$insert->execute([
				':full_name'	 => $fullName,
				':user_name'	 => $userName,
				':email'		 => $email,
				':password_hash' => \MvcCore\Ext\Auths\Basics\User::EncodePasswordToHash(
					$password
				),
				':avatar_url'	=> $avatarUrl,
			]);

			$rawNewId = $db->lastInsertId();
			if (is_numeric($rawNewId))
				$newId = intval($rawNewId);

			$db->commit();

		} catch (\Exception $e) {
			$db->rollBack();
			throw $e;
		}
		return $newId;
	}

	/**
	 * Get user model instance from database or any other users list
	 * resource by submitted and cleaned `$userName` field value.
	 * @param string $userName Submitted and cleaned username. Characters `' " ` < > \ = ^ | & ~` are automatically encoded to html entities by default `\MvcCore\Ext\Auths\Basic` sign in form.
	 * @return \App\Models\User|NULL
	 */
	public static function GetByUserName ($userName) {
		return Statement::Prepare([
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
		return Statement::Prepare([
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