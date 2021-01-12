<?php

namespace App\Models;

use \MvcCore\Ext\Database\Attributes as Attrs;

/** 
 * @connection my57
 */
#[Attrs\Connection('my57')]
class Base extends \MvcCore\Ext\Database\Models\MySql {

}