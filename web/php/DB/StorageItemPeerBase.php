<?php

namespace Wikidot\DB;




use Ozone\Framework\Database\BaseDBPeer;

/**
 * Base peer Class mapped to the database table storage_item.
 */
class StorageItemPeerBase extends BaseDBPeer
{
    public static $peerInstance;

    protected function internalInit()
    {
        $this->tableName='storage_item';
        $this->objectName='Wikidot\\DB\\StorageItem';
        $this->primaryKeyName = 'item_id';
        $this->fieldNames = array( 'item_id' ,  'date' ,  'timeout' ,  'data' );
        $this->fieldTypes = array( 'item_id' => 'varchar(256)',  'date' => 'timestamp',  'timeout' => 'int',  'data' => 'bytea');
        $this->defaultValues = array();
    }

    public static function instance()
    {
        if (self::$peerInstance == null) {
            $className = 'Wikidot\\DB\\StorageItemPeer';
            self::$peerInstance = new $className();
        }
        return self::$peerInstance;
    }
}
