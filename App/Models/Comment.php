<?php

namespace App\Models;

class Comment extends \MvcCore\Model
{
	/** @var int */
	public $Id;
	/** @var int */
	public $IdPost;
	/** @var int */
	public $IdUser;
	/** @var int */
	public $Active;
	/** @var \DateTime */
	public $Created;
	/** @var string */
	public $Title;
	/** @var string */
	public $Content;
	/** @var string */
	public $UserName;
	/** @var string */
	public $FullName;
	/** @var string */
	public $AvatarUrl;

	/**
	 * Get all comments in database as array, keyed by $Comment->Id.
	 * @param string $orderCol 'created' by default.
	 * @param string $orderDir 'desc' by default.
	 * @param string $filterCol NULL by default.
	 * @param string $filterVal NULL by default.
	 * @return \App\Models\Comment[]
	 */
	public static function GetAll (
		$orderCol = 'created', $orderDir = 'desc', $filterCol = NULL, $filterVal = NULL
	) {
		$sql = [
			"SELECT								",
			"	c.*,							",
			"	u.user_name,					",
			"	u.full_name,					",
			"	u.avatar_url					",
			"FROM comments c					",
			"LEFT JOIN users u ON				",
			"	c.id_user = u.id				",
			"",
			"ORDER BY {$orderCol} {$orderDir};	",
		];
		$params = [];
		if ($filterCol !== NULL && $filterVal !== NULL) {
			if ($filterCol == 'user_name') {
				$filterCol = "u.{$filterCol}";
			} else {
				$filterCol = "c.{$filterCol}";
			}
			$sql[8] = "WHERE {$filterCol} = :val ";
			$params[':val'] = $filterVal;
		}
		$select = self::GetConnection()->prepare(implode("\n", $sql));
		$select->execute($params);
		$rows = $select->fetchAll(\PDO::FETCH_ASSOC);
		$comments = [];
		foreach ($rows as $row) {
			/** @var $post \App\Models\Comment */
			$comment = (new self)->SetUp(
				$row, \MvcCore\IModel::KEYS_CONVERSION_UNDERSCORES_TO_PASCALCASE, FALSE
			);
			$comments[$comment->Id] = $comment;
		}
		return $comments;
	}

	/**
	 * Get all post comments ordered by created date ascendently.
	 * @param int $idPost
	 * @param bool $activeOnly `TRUE` by default.
	 * @return \App\Models\Comment[]
	 */
	public static function GetByPostId ($idPost, $activeOnly = TRUE) {
		$sql = [
			"SELECT						",
			"	c.*,					",
			"	u.user_name,			",
			"	u.full_name,			",
			"	u.avatar_url			",
			"FROM comments c			",
			"LEFT JOIN users u ON		",
			"	c.id_user = u.id		",
			"WHERE c.id_post = :id_post	",
			"ORDER BY c.created DESC;	",
		];
		if ($activeOnly) $sql[8] .= " AND c.active = 1 ";
		$select = self::GetConnection()->prepare(
			implode("\n", $sql)
		);
		$select->execute([
			':id_post' => $idPost
		]);
		$rows = $select->fetchAll(\PDO::FETCH_ASSOC);
		$comments = [];
		foreach ($rows as $row) {
			/** @var $post \App\Models\Post */
			$comment = (new self)->SetUp(
				$row, \MvcCore\IModel::KEYS_CONVERSION_UNDERSCORES_TO_PASCALCASE, FALSE
			);
			$comments[$comment->Id] = $comment;
		}
		return $comments;
	}

	/**
	 * @param int $idPost
	 * @param int $idUser
	 * @param string $title
	 * @param string $content
	 * @return int
	 */
	public static function AddNew ($idPost, $idUser, $title, $content) {
		$newId = NULL;
		$db = self::GetConnection();
		try {
			$db->beginTransaction();
			$sql = $sql = implode("\n", [
				"INSERT INTO comments (				",
				"	id_post, id_user, created,		",
				"	active, title, content			",
				") VALUES (							",
				"	:id_post, :id_user, :created,	",
				"	1, :title, :content				",
				");									",
			]);
			$insert = self::GetConnection()->prepare($sql);
			$insert->execute([
				':id_post'	=> $idPost,
				':id_user'	=> $idUser,
				':created'	=> date('Y-m-d H:i:s', time()),
				':title'	=> $title,
				':content'	=> $content,
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
	 * Get post comment.
	 * @param int $idComment
	 * @param bool $activeOnly `TRUE` by default.
	 * @return \App\Models\Comment
	 */
	public static function GetById ($idComment, $activeOnly = TRUE) {
		$sql = [
			"SELECT 							",
			"	c.*, u.user_name, u.full_name	",
			"FROM comments c					",
			"LEFT JOIN users u ON				",
			"	c.id_user = u .id				",
			"WHERE c.id = :id					",
		];
		if ($activeOnly) $sql[5] .= " AND c.active = 1 ";
		$select = self::GetConnection()->prepare(
			implode("\n", $sql)
		);
		$select->execute([
			':id' => $idComment
		]);
		$row = $select->fetch(\PDO::FETCH_ASSOC);
		if (!$row) return NULL;
		/** @var $comment \App\Models\Comment */
		$comment = (new self)->SetUp(
			$row, \MvcCore\IModel::KEYS_CONVERSION_UNDERSCORES_TO_PASCALCASE, TRUE
		);
		return $comment;
	}

	/**
	 * Update post with completed Id or insert new one if no Id defined.
	 * Return Id as result.
	 * @return int
	 */
	public function Save () {
		$this->Init();
		if (isset($this->Id)) {
			$this->update();
		} else {
			$this->Id = $this->insert();
		}
		return $this->Id;
	}

	/**
	 * Update all public defined properties.
	 * @return bool
	 */
	protected function update () {
		$columnsSql = [];
		$params = [];
		$touched = $this->GetTouched(FALSE, TRUE);
		if (count($touched) === 0) return TRUE;
		foreach ($touched as $key => $value) {
			$keyUnderscored = \MvcCore\Tool::GetUnderscoredFromPascalCase($key);
			if ($keyUnderscored == 'id') continue;
			$columnsSql[] = "{$keyUnderscored} = :{$keyUnderscored}";
			if ($value instanceof \DateTime) {
				$dbValue = $value->format('Y-m-d H:i:s');
			} else {
				$dbValue = (string) $value;
			}
			$params[":{$keyUnderscored}"] = $dbValue;
		}
		$params[':id'] = $this->Id;
		$sqlColumns = implode(',', $columnsSql);
		$sql = "UPDATE comments SET {$sqlColumns} WHERE id = :id";
		$update = $this->connection->prepare($sql);
		$affectedRows = $update->execute($params);
		return $affectedRows > 0;
	}

	/**
	 * Insert only filled values, return new post id.
	 * @return int
	 */
	protected function insert() {
		$columnsSql = [];
		$params = [];
		foreach ($this->GetValues() as $key => $value) {
			$keyUnderscored = \MvcCore\Tool::GetUnderscoredFromPascalCase($key);
			$columnsSql[] = $keyUnderscored;
			if ($value instanceof \DateTime) {
				$dbValue = $value->format('Y-m-d H:i:s');
			} else {
				$dbValue = (string) $value;
			}
			$params[':' . $keyUnderscored] = $dbValue;
		}
		$sqlColumns = implode(',', $columnsSql);
		$sqlValues = ':' . implode(', :', $columnsSql);
		$sql = "INSERT INTO comments ({$sqlColumns}) VALUES ({$sqlValues});";
		$newId = NULL;
		try {
			$this->connection->beginTransaction();
			$insertCommand = $this->connection->prepare($sql);
			$insertCommand->execute($params);
			$newIdRaw = $this->connection->lastInsertId();
			if (is_numeric($newIdRaw))
				$newId = intval($newIdRaw);
			$this->connection->commit();
		} catch (\Exception $e) {
			$this->connection->rollBack();
			throw $e;
		}
		return $newId;
	}
}
