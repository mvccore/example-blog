<?php

namespace App\Models;

use \MvcCore\Ext\Database\{
	Statement, 
	Attributes as Attrs
};

use function \MvcCore\Ext\Database\FuncHelpers\Table as Table,
			 \MvcCore\Ext\Database\FuncHelpers\Columns as Columns;

/** 
 * @table posts, comments
 */
#[Attrs\Table('posts', 'comments')]
class Post extends \App\Models\Base {

	/**
	 * Default flags for properties with database columns anotations
	 * and default flags for any other selected data to init into properties.
	 * @var int
	 */
	protected static $defaultPropsFlags = (
		self::PROPS_PUBLIC | 
		self::PROPS_CONVERT_UNDERSCORES_TO_PASCALCASE
	);

	/** 
	 * @column id
	 * @keyPrimary true
	 * @var ?int
	 */
	#[Attrs\Column('id'), Attrs\KeyPrimary(TRUE)]
	public ?int $Id;

	/**
	 * @column path
	 * @keyUnique path
	 * @var ?string
	 */
	#[Attrs\Column('path'), Attrs\KeyUnique('path')]
	public string $Path;

	/**
	 * @column title
	 * @var ?string
	 */
	#[Attrs\Column('title')]
	public string $Title;

	/**
	 * @column created
	 * @format Y-m-d H:i:s.u, UTC
	 * @var \DateTime
	 */
	#[Attrs\Column('created'), Attrs\Format('Y-m-d H:i:s.u', 'UTC')]
	public \DateTime $Created;

	/**
	 * @column updated
	 * @format Y-m-d H:i:s.u, UTC
	 * @var \DateTime
	 */
	#[Attrs\Column('updated'), Attrs\Format('Y-m-d H:i:s.u', 'UTC')]
	public \DateTime $Updated;

	/**
	 * @column content
	 * @var string
	 */
	#[Attrs\Column('content')]
	public string $Content;

	/**
	 * @column perex
	 * @var string
	 */
	#[Attrs\Column('perex')]
	public ?string $Perex;

	/**
	 * @var int|NULL
	 */
	public ?int $CommentsCount;


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
		return Statement::Prepare([
				"SELECT 									",
				"	p1.*,									",
				"	(CASE p2.comments_count					",
				"		WHEN NULL THEN 0					",
				"		ELSE p2.comments_count				",
				"	END) AS comments_count					",
				"FROM (										",
				"	SELECT p.".Columns(",p.")."				",
				"	FROM ".Table(0)." p						",
				") p1										",
				"LEFT JOIN (								",
				"	SELECT 									",
				"		c.id_post, 							",
				"		COUNT(c.id_post) AS comments_count	",
				"	FROM ".Table(1)." c			 			",
				"	GROUP BY c.id_post						",
				") p2 ON									",
				"	p1.id = p2.id_post						",
				"ORDER BY {$orderCol} {$orderDir};			",
			])
			->FetchAll()
			->ToInstances(
				get_called_class(), 
				self::$defaultPropsFlags,
				'id', 'int'
			);
	}

	/**
	 * Get single post instance by given id or null if no record by id in database.
	 * @param int $id
	 * @return \App\Models\Post|NULL
	 */
	public static function GetById ($id) {
		return Statement::Prepare([
				"SELECT								",
				"	p.".Columns(",p.").", (			",
				"		SELECT COUNT(c.id)			",
				"		FROM ".Table(1)." c			",
				"		WHERE						",
				"			c.id_post = p.id AND	",
				"			c.active = 1			",
				"	) AS comments_count				",
				"FROM ".Table(0)." p				",
				"WHERE p.id = :id					",
			])
			->FetchOne([':id' => $id])
			->ToInstance(
				get_called_class(),
				self::$defaultPropsFlags |
				self::PROPS_INITIAL_VALUES
			);
	}

	/**
	 * Get single post instance by given path or null if no record by id in database.
	 * @param string $path
	 * @return \App\Models\Post|\MvcCore\IModel|NULL
	 */
	public static function GetByPath ($path) {
		return Statement::Prepare([
				"SELECT								",
				"	p.".Columns(",p.").", (			",
				"		SELECT COUNT(c.id)			",
				"		FROM ".Table(1)." c			",
				"		WHERE						",
				"			c.id_post = p.id AND	",
				"			c.active = 1			",
				"	) AS comments_count				",
				"FROM ".Table(0)." p				",
				"WHERE p.path = :path				",
			])
			->FetchOne([':path' => $path])
			->ToInstance(
				get_called_class(),
				self::$defaultPropsFlags
			);
	}

	/**
	 * Get all post comments ordered by created date ascendently.
	 * @return \App\Models\Comment[]
	 */
	public function GetComments () {
		return \App\Models\Comment::GetByPostId($this->Id, TRUE);
	}
}
