<?php
/**
 * Wikidot - free wiki collaboration software
 * Copyright (c) 2008, Wikidot Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * For more information about licensing visit:
 * http://www.wikidot.org/license
 *
 * @category Wikidot
 * @package Wikidot
 * @version \$Id\$
 * @copyright Copyright (c) 2008, Wikidot Inc.
 * @license http://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License
 */

namespace DB;

use BaseDBPeer;




/**
 * Base peer class mapped to the database table global_user_block.
 */
class GlobalUserBlockPeerBase extends BaseDBPeer {
    public static $peerInstance;

    protected function internalInit(){
        $this->tableName='global_user_block';
        $this->objectName='DB\\GlobalUserBlock';
        $this->primaryKeyName = 'block_id';
        $this->fieldNames = array( 'block_id' ,  'site_id' ,  'user_id' ,  'reason' ,  'date_blocked' );
        $this->fieldTypes = array( 'block_id' => 'serial',  'site_id' => 'int',  'user_id' => 'int',  'reason' => 'text',  'date_blocked' => 'timestamp');
        $this->defaultValues = array();
    }

    public static function instance(){
        if(self::$peerInstance == null){
            $className = "DB\\GlobalUserBlockPeer";
            self::$peerInstance = new $className();
        }
        return self::$peerInstance;
    }

}