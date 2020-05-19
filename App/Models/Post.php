<?php

namespace App\Models;

class Post extends \MvcCore\Model
{
	/** @var int */
	public $Id;
	/** @var string */
	public $Path;
	/** @var string */
	public $Title;
	/** @var \DateTime */
	public $Created;
	/** @var \DateTime */
	public $Updated;
	/** @var string */
	public $Content;
	/** @var string */
	public $Perex;
	/** @var int */
	public $CommentsCount;

	/** @return string */
	public function Url () {
		return \MvcCore\Application::GetInstance()
			->GetRouter()
			->Url('front_post', ['path' => $this->Path]);
	}

	/**
	 * Get all post in database as array, keyed by $post->Id.
	 * @param string $orderCol 'created' by default.
	 * @param string $orderDir 'desc' by default.
	 * @return \App\Models\Post[]
	 */
	public static function GetAll ($orderCol = 'created', $orderDir = 'desc') {
		$sql = implode("\n", [
			"SELECT									",
			"	p.*,								",
			"	COUNT(c.id) as comments_count		",
			"FROM posts p							",
			"LEFT OUTER JOIN comments c ON			",
			"	c.id_post = p.id					",
			"GROUP BY p.id							",
			"ORDER BY {$orderCol} {$orderDir}		",
		]);
		$select = self::GetConnection()->prepare($sql);
		$select->execute();
		$rows = $select->fetchAll(\PDO::FETCH_ASSOC);
		$posts = [];
		foreach ($rows as $row) {
			/** @var $post \App\Models\Post */
			$post = (new self)->SetUp(
				$row, \MvcCore\IModel::KEYS_CONVERSION_UNDERSCORES_TO_PASCALCASE, FALSE
			);
			$posts[$post->Id] = $post;
		}
		return $posts;
	}

	/**
	 * Get single post instance by given id or null if no record by id in database.
	 * @param int $id
	 * @return \App\Models\Post|null
	 */
	public static function GetById ($id) {
		$sql = implode("\n", [
			"SELECT								",
			"	p.*, (							",
			"		SELECT COUNT(c.id)			",
			"		FROM comments c				",
			"		WHERE						",
			"			c.id_post = p.id AND	",
			"			c.active = 1			",
			"	) AS comments_count				",
			"FROM posts p						",
			"WHERE p.id = :id					",
		]);
		$select = self::GetConnection()->prepare($sql);
		$select->execute([
			":id" => $id,
		]);
		$row = $select->fetch(\PDO::FETCH_ASSOC);
		if (!$row) return NULL;
		/** @var $post \App\Models\Post */
		$post = (new self)->SetUp(
			$row, \MvcCore\IModel::KEYS_CONVERSION_UNDERSCORES_TO_PASCALCASE, TRUE
		);
		return $post;
	}

	/**
	 * Get single post instance by given path or null if no record by id in database.
	 * @param string $path
	 * @return \App\Models\Post|null
	 */
	public static function GetByPath ($path) {
		$sql = implode("\n", [
			"SELECT								",
			"	p.*, (							",
			"		SELECT COUNT(c.id)			",
			"		FROM comments c				",
			"		WHERE						",
			"			c.id_post = p.id AND	",
			"			c.active = 1			",
			"	) AS comments_count				",
			"FROM posts p						",
			"WHERE p.path = :path				",
		]);
		$select = self::GetConnection()->prepare($sql);
		$select->execute([
			":path" => $path,
		]);
		$row = $select->fetch(\PDO::FETCH_ASSOC);
		if (!$row) return NULL;
		/** @var $post \App\Models\Post */
		$post = (new self)->SetUp(
			$row, \MvcCore\IModel::KEYS_CONVERSION_UNDERSCORES_TO_PASCALCASE, TRUE
		);
		return $post;
	}

	/**
	 * Get all post comments ordered by created date ascendently.
	 * @return \App\Models\Comment[]
	 */
	public function GetComments () {
		return \App\Models\Comment::GetByPostId($this->Id, TRUE);
	}

	/**
	 * Delete database row by post Id. Return affected rows count.
	 * @return bool
	 */
	public function Delete () {
		$this->Init();
		$update = $this->connection->prepare(
			"DELETE FROM posts WHERE id = :id"
		);
		return $update->execute([
			":id"	=> $this->Id,
		]);
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
		$sql = "UPDATE posts SET {$sqlColumns} WHERE id = :id";
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
		$sql = "INSERT INTO posts ({$sqlColumns}) VALUES ({$sqlValues});";
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
