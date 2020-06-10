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

use BaseDBObject;
use Criteria;


/**
 * Base class mapped to the database table ucookie.
 */
class UcookieBase extends BaseDBObject {

    protected function internalInit(){
        $this->tableName='ucookie';
        $this->peerName = 'DB\\UcookiePeer';
        $this->primaryKeyName = 'ucookie_id';
        $this->fieldNames = array( 'ucookie_id' ,  'site_id' ,  'session_id' ,  'date_granted' );

        //$this->fieldDefaultValues=
    }



            public function getSite(){
            if(is_array($this->prefetched)){
            if(in_array('site', $this->prefetched)){
                if(in_array('site', $this->prefetchedObjects)){
                    return $this->prefetchedObjects['site'];
                } else {

                    $obj = new Site($this->sourceRow);
                    $obj->setNew(false);
                    //$obj->prefetched = $this->prefetched;
                    //$obj->sourceRow = $this->sourceRow;
                    $this->prefetchedObjects['site'] = $obj;
                    return $obj;
                }
            }
        }
        $foreignPeerClassName = 'DB\\SitePeer';
        $fpeer = new $foreignPeerClassName();

        $criteria = new Criteria();

        $criteria->add("site_id", $this->fieldValues['site_id']);

        $result = $fpeer->selectOneByCriteria($criteria);
        return $result;
    }

        public function setSite($primaryObject){
            $this->fieldValues['site_id'] = $primaryObject->getFieldValue('site_id');
    }
            public function getOzoneSession(){
            if(is_array($this->prefetched)){
            if(in_array('ozone_session', $this->prefetched)){
                if(in_array('ozone_session', $this->prefetchedObjects)){
                    return $this->prefetchedObjects['ozone_session'];
                } else {

                    $obj = new OzoneSession($this->sourceRow);
                    $obj->setNew(false);
                    //$obj->prefetched = $this->prefetched;
                    //$obj->sourceRow = $this->sourceRow;
                    $this->prefetchedObjects['ozone_session'] = $obj;
                    return $obj;
                }
            }
        }
        $foreignPeerClassName = 'DB\\OzoneSessionPeer';
        $fpeer = new $foreignPeerClassName();

        $criteria = new Criteria();

        $criteria->add("session_id", $this->fieldValues['session_id']);

        $result = $fpeer->selectOneByCriteria($criteria);
        return $result;
    }

        public function setOzoneSession($primaryObject){
            $this->fieldValues['session_id'] = $primaryObject->getFieldValue('session_id');
    }



    public function getUcookieId() {
        return $this->getFieldValue('ucookie_id');
    }

    public function setUcookieId($v1, $raw=false) {
        $this->setFieldValue('ucookie_id', $v1, $raw);
    }


    public function getSiteId() {
        return $this->getFieldValue('site_id');
    }

    public function setSiteId($v1, $raw=false) {
        $this->setFieldValue('site_id', $v1, $raw);
    }


    public function getSessionId() {
        return $this->getFieldValue('session_id');
    }

    public function setSessionId($v1, $raw=false) {
        $this->setFieldValue('session_id', $v1, $raw);
    }


    public function getDateGranted() {
        return $this->getFieldValue('date_granted');
    }

    public function setDateGranted($v1, $raw=false) {
        $this->setFieldValue('date_granted', $v1, $raw);
    }




}
