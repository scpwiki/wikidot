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
 * Base peer class mapped to the database table admin_notification.
 */
class AdminNotificationPeerBase extends BaseDBPeer {
    public static $peerInstance;

    protected function internalInit(){
        $this->tableName='admin_notification';
        $this->objectName='DB\\AdminNotification';
        $this->primaryKeyName = 'notification_id';
        $this->fieldNames = array( 'notification_id' ,  'site_id' ,  'body' ,  'type' ,  'viewed' ,  'date' ,  'extra' ,  'notify_online' ,  'notify_feed' ,  'notify_email' );
        $this->fieldTypes = array( 'notification_id' => 'serial',  'site_id' => 'int',  'body' => 'text',  'type' => 'varchar(50)',  'viewed' => 'boolean',  'date' => 'timestamp',  'extra' => 'bytea',  'notify_online' => 'boolean',  'notify_feed' => 'boolean',  'notify_email' => 'boolean');
        $this->defaultValues = array( 'viewed' => 'false',  'notify_online' => 'false',  'notify_feed' => 'false',  'notify_email' => 'false');
    }

    public static function instance(){
        if(self::$peerInstance == null){
            $className = "DB\\AdminNotificationPeer";
            self::$peerInstance = new $className();
        }
        return self::$peerInstance;
    }

}