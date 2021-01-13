<?php

namespace App\Models;

use \MvcCore\Ext\Models\Db\Attrs;

/** 
 * @connection my57
 */
#[Attrs\Connection('my57')]
class Base
extends \MvcCore\Ext\Models\Db\Models\MySql
//extends \MvcCore\Ext\Models\Db\Models\SqlSrv
{

}