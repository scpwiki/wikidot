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
 * Base peer class mapped to the database table front_forum_feed.
 */
class FrontForumFeedPeerBase extends BaseDBPeer {
    public static $peerInstance;

    protected function internalInit(){
        $this->tableName='front_forum_feed';
        $this->objectName='DB\\FrontForumFeed';
        $this->primaryKeyName = 'feed_id';
        $this->fieldNames = array( 'feed_id' ,  'page_id' ,  'title' ,  'label' ,  'description' ,  'categories' ,  'parmhash' ,  'site_id' );
        $this->fieldTypes = array( 'feed_id' => 'serial',  'page_id' => 'int',  'title' => 'varchar(256)',  'label' => 'varchar(90)',  'description' => 'varchar(256)',  'categories' => 'varchar(100)',  'parmhash' => 'varchar(100)',  'site_id' => 'int');
        $this->defaultValues = array();
    }

    public static function instance(){
        if(self::$peerInstance == null){
            $className = "DB\\FrontForumFeedPeer";
            self::$peerInstance = new $className();
        }
        return self::$peerInstance;
    }

}