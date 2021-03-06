<?php

namespace Wikidot\DB;




use Ozone\Framework\Database\BaseDBPeer;

/**
 * Base peer Class mapped to the database table page_external_link.
 */
class PageExternalLinkPeerBase extends BaseDBPeer
{
    public static $peerInstance;

    protected function internalInit()
    {
        $this->tableName='page_external_link';
        $this->objectName='Wikidot\\DB\\PageExternalLink';
        $this->primaryKeyName = 'link_id';
        $this->fieldNames = array( 'link_id' ,  'site_id' ,  'page_id' ,  'to_url' ,  'date' );
        $this->fieldTypes = array( 'link_id' => 'serial',  'site_id' => 'int',  'page_id' => 'int',  'to_url' => 'varchar(512)',  'date' => 'timestamp');
        $this->defaultValues = array();
    }

    public static function instance()
    {
        if (self::$peerInstance == null) {
            $className = 'Wikidot\\DB\\PageExternalLinkPeer';
            self::$peerInstance = new $className();
        }
        return self::$peerInstance;
    }
}
