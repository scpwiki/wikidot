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
 * Base peer class mapped to the database table user_abuse_flag.
 */
class UserAbuseFlagPeerBase extends BaseDBPeer {
    public static $peerInstance;

    protected function internalInit(){
        $this->tableName='user_abuse_flag';
        $this->objectName='DB\\UserAbuseFlag';
        $this->primaryKeyName = 'flag_id';
        $this->fieldNames = array( 'flag_id' ,  'user_id' ,  'target_user_id' ,  'site_id' ,  'site_valid' ,  'global_valid' );
        $this->fieldTypes = array( 'flag_id' => 'serial',  'user_id' => 'int',  'target_user_id' => 'int',  'site_id' => 'int',  'site_valid' => 'boolean',  'global_valid' => 'boolean');
        $this->defaultValues = array( 'site_valid' => 'true',  'global_valid' => 'true');
    }

    public static function instance(){
        if(self::$peerInstance == null){
            $className = "DB\\UserAbuseFlagPeer";
            self::$peerInstance = new $className();
        }
        return self::$peerInstance;
    }

}