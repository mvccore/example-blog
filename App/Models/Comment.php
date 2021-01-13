<?php

namespace App\Models;

use \MvcCore\Ext\Models\Db\{Statement, Attrs};
use function \MvcCore\Ext\Models\Db\FuncHelpers\{Table, Columns};

/** 
 * @table comments, users
 */
#[Attrs\Table('comments', 'users')]
class Comment extends \App\Models\Base {
	
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
	 * @var int
	 * @column id
	 * @keyPrimary true
	 */
	#[Attrs\Column('id'), Attrs\KeyPrimary]
	public ?int $Id;
	
	/** 
	 * @var int|NULL
	 * @column id_post
	 */
	#[Attrs\Column('id_post')]
	public int $IdPost;
	
	/** 
	 * @var int
	 * @column id_user
	 */
	#[Attrs\Column('id_user')]
	public int $IdUser;
	
	/** 
	 * @var bool
	 * @column active
	 */
	#[Attrs\Column('active')]
	public bool $Active;
	
	/** 
	 * @var \DateTime
	 * @column created
	 * @format +Y-m-d H:i:s, UTC
	 */
	#[Attrs\Column('created'),Attrs\Format('+Y-m-d H:i:s', 'UTC')]
	public \DateTime $Created;
	
	/** 
	 * @var string
	 * @column title
	 */
	#[Attrs\Column('title')]
	public string $Title;
	
	/** 
	 * @var string|NULL
	 * @column content
	 */
	#[Attrs\Column('content')]
	public ?string $Content;
	
	/**
	 * @var string|NULL
	 */
	public ?string $UserName;
	
	/**
	 * @var string|NULL
	 */
	public ?string $FullName;
	
	/** 
	 * @var string|NULL
	 */
	public ?string $AvatarUrl;

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
			"	c.".Columns(",c.").",			",
			"	u.user_name,					",
			"	u.full_name,					",
			"	u.avatar_url					",
			"FROM ".Table(0)." c				",
			"LEFT JOIN ".Table(1)." u ON		",
			"	c.id_user = u.id				",
			"",// dynamic condition
			"ORDER BY {$orderCol} {$orderDir};	",
		];
		$params = [];
		if ($filterCol !== NULL && $filterVal !== NULL) {
			if ($filterCol == 'user_name') {
				$filterCol = "u.{$filterCol}";
			} else {
				$filterCol = "c.{$filterCol}";
			}
			$sql[count($sql) - 2] = "WHERE {$filterCol} = :val ";
			$params[':val'] = $filterVal;
		}
		return Statement::Prepare($sql)
			->FetchAll($params)
			->ToInstances(
				get_called_class(),
				self::PROPS_PUBLIC |
				self::PROPS_CONVERT_UNDERSCORES_TO_PASCALCASE,
				'id', 'int'
			);
	}

	/**
	 * Get all post comments ordered by created date ascendently.
	 * @param int $idPost
	 * @param bool $activeOnly `TRUE` by default.
	 * @return \App\Models\Comment[]
	 */
	public static function GetByPostId ($idPost, $activeOnly = TRUE) {
		$sql = [
			"SELECT							",
			"	c.".Columns(",c.").",		",
			"	u.user_name,				",
			"	u.full_name,				",
			"	u.avatar_url				",
			"FROM ".Table(0)." c			",
			"LEFT JOIN ".Table(1)." u ON	",
			"	c.id_user = u.id			",
			"WHERE c.id_post = :id_post		",
			"ORDER BY c.created ASC;		",
		];
		if ($activeOnly) $sql[8] .= " AND c.active = 1 ";
		return Statement::Prepare($sql)
			->FetchAll([':id_post' => $idPost])
			->ToInstances(
				get_called_class(),
				self::PROPS_PUBLIC |
				self::PROPS_CONVERT_UNDERSCORES_TO_PASCALCASE,
				'id', 'int'
			);
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
			"	c.".Columns(",c.").",			",
			"	u.user_name, u.full_name		",
			"FROM ".Table(0)." c				",
			"LEFT JOIN ".Table(1)." u ON		",
			"	c.id_user = u .id				",
			"WHERE c.id = :id					",
		];
		if ($activeOnly) $sql[5] .= " AND c.active = 1 ";
		return Statement::Prepare($sql)
			->FetchOne([':id' => $idComment])
			->ToInstance(
				get_called_class(),
				self::PROPS_PUBLIC | 
				self::PROPS_CONVERT_UNDERSCORES_TO_PASCALCASE
			);
	}
}
