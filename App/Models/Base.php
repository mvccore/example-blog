<?php

namespace App\Models;

use \MvcCore\Ext\Models\Db\Attrs;

/** 
 * @connection lite
 */
#[Attrs\Connection('lite')]
class Base
extends \MvcCore\Ext\Models\Db\Models\SQLite
//extends \MvcCore\Ext\Models\Db\Models\MySql
//extends \MvcCore\Ext\Models\Db\Models\SqlSrv
//extends \MvcCore\Ext\Models\Db\Models\PgSql
{

}