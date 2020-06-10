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
 * @category Ozone
 * @package Ozone_Db
 * @version $Id$
 * @copyright Copyright (c) 2008, Wikidot Inc.
 * @license http://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License
 */


/**
 * Base object for all database OM objects representing views. It implements
 * most of the database logic - getting field valuse etc.
 */
abstract class BaseDBViewObject {

    /**
     * List of the fields of this particular object/view.
     * @var array
     */
    protected $fieldNames;

    /**
     * Contains the values of the fields.
     * @var array
     */
    protected $fieldValues = array();

    /**
     * Name of the corresponding view.
     * $var string
     */
    protected $tableName;

    /**
     * Class name of the corresponding "Peer".
     * @var string
     */
    protected $peerName;

    /**
     * Temporary storage - used to store data associated with object but not written into
     * the database. That is why it is calles "temporary".
     * @var array
     */
    protected $temporaryStorage = array();

    /**
     * Function used to set values of $tableName
     * and $fieldNames.
     */
    protected abstract function internalInit();

    /**
     * Default constructor. If $row is non-null then object properties are filled with
     * values of the row. If $row is null, object properties are filled with default values.
     * @param array $row initial values for the object
     */
    public function __construct($row) {
        $this->internalInit();
        if ($row != null) {
            $this->populate($row);
        } else {
            throw new OzoneException("The view object ".$this->tableName." can not be initialized as 'empty' object");
        }

    }

    /**
     * Populate object data with values from the $row.
     * @param array $row values to fill the object data
     */
    public function populate($row) {
        ## copy the values from $row, but only with keys that exist in $fieldList
        foreach ($this->fieldNames as $field) {
            $val = $row[$this->tableName.".".$field];
            if ($val == null) {
                $val = $row[$field];
            }
            if($val == "NULL"){
                $val = null;
            }
            $this->fieldValues[$field] = $val;
        }
    }

    /**
     * Gets field value.
     * @param string $fieldName name of the field.
     * @return mixed
     */
    public function getFieldValue($filedName){
        return $this->fieldValues[$filedName];
    }

    /**
     * Gets all field values as an array.
     * @return array array of the field values.
     */
    public function getFieldValuesArray(){
        return $this->fieldValues;
    }

    /**
     * Get value from the temporary storage.
     * @param mixed $key
     * @return mixed
     */
    public function getTemp($key){
        return $this->temporaryStorage[$key];
    }

    /**
     * Sets key-value pair in the temporary storage
     * @param mixed $key
     * @param mixed $value
     */
    public function setTemp($key, $value){
        $this->temporaryStorage[$key] = $value;
    }

    /**
     * Clears temporary storage. If $key is supplied - only one key-value is cleared.
     * If $key is null - all the storage is cleared.
     */
    public function clearTemp($key=null){
        if($key == null){
            $this->temporaryStorage = array();
        } else{
            unset($this->temporaryStorage[$key]);
        }
    }

    public function setNew($val=null){
        return;
    }

}
