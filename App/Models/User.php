<?php

namespace App\Models;

class User extends \MvcCore\Ext\Auths\Basics\User
{
	protected $avatarUrl = NULL;

	public function SetAvatarUrl ($avatarUrl) {
		$this->avatarUrl = $avatarUrl;
		return $this;
	}

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
		$sql = implode("\n", [
			"SELECT *						",
			"FROM users u					",
			"WHERE u.user_name = :user_name;",
		]);
		$select = self::GetConnection()->prepare($sql);
		$select->execute([':user_name' => $userName]);
		$row = $select->fetch(\PDO::FETCH_ASSOC);
		if (!$row) return NULL;
		/** @var $user \App\Models\User */
		$user = (new static)
			->SetUp($row, \MvcCore\IModel::KEYS_CONVERSION_UNDERSCORES_TO_CAMELCASE, TRUE);
		return $user;
	}

	/**
	 * @param string $email
	 * @return \App\Models\User|NULL
	 */
	public static function GetByUserEmail ($email) {
		$sql = implode("\n", [
			"SELECT *				",
			"FROM users u			",
			"WHERE u.email = :email;",
		]);
		$select = self::GetConnection()->prepare($sql);
		$select->execute([':email' => $email]);
		$row = $select->fetch(\PDO::FETCH_ASSOC);
		if (!$row) return NULL;
		/** @var $user \App\Models\User */
		$user = (new static)
			->SetUp($row, \MvcCore\IModel::KEYS_CONVERSION_UNDERSCORES_TO_CAMELCASE, TRUE);
		return $user;
	}
}