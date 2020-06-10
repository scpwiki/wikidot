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




/**
 * Base class mapped to the database table page_edit_lock.
 */
class PageEditLockBase extends BaseDBObject {

    protected function internalInit(){
        $this->tableName='page_edit_lock';
        $this->peerName = 'DB\\PageEditLockPeer';
        $this->primaryKeyName = 'lock_id';
        $this->fieldNames = array( 'lock_id' ,  'page_id' ,  'mode' ,  'section_id' ,  'range_start' ,  'range_end' ,  'page_unix_name' ,  'site_id' ,  'user_id' ,  'user_string' ,  'session_id' ,  'date_started' ,  'date_last_accessed' ,  'secret' );

        //$this->fieldDefaultValues=
    }






    public function getLockId() {
        return $this->getFieldValue('lock_id');
    }

    public function setLockId($v1, $raw=false) {
        $this->setFieldValue('lock_id', $v1, $raw);
    }


    public function getPageId() {
        return $this->getFieldValue('page_id');
    }

    public function setPageId($v1, $raw=false) {
        $this->setFieldValue('page_id', $v1, $raw);
    }


    public function getMode() {
        return $this->getFieldValue('mode');
    }

    public function setMode($v1, $raw=false) {
        $this->setFieldValue('mode', $v1, $raw);
    }


    public function getSectionId() {
        return $this->getFieldValue('section_id');
    }

    public function setSectionId($v1, $raw=false) {
        $this->setFieldValue('section_id', $v1, $raw);
    }


    public function getRangeStart() {
        return $this->getFieldValue('range_start');
    }

    public function setRangeStart($v1, $raw=false) {
        $this->setFieldValue('range_start', $v1, $raw);
    }


    public function getRangeEnd() {
        return $this->getFieldValue('range_end');
    }

    public function setRangeEnd($v1, $raw=false) {
        $this->setFieldValue('range_end', $v1, $raw);
    }


    public function getPageUnixName() {
        return $this->getFieldValue('page_unix_name');
    }

    public function setPageUnixName($v1, $raw=false) {
        $this->setFieldValue('page_unix_name', $v1, $raw);
    }


    public function getSiteId() {
        return $this->getFieldValue('site_id');
    }

    public function setSiteId($v1, $raw=false) {
        $this->setFieldValue('site_id', $v1, $raw);
    }


    public function getUserId() {
        return $this->getFieldValue('user_id');
    }

    public function setUserId($v1, $raw=false) {
        $this->setFieldValue('user_id', $v1, $raw);
    }


    public function getUserString() {
        return $this->getFieldValue('user_string');
    }

    public function setUserString($v1, $raw=false) {
        $this->setFieldValue('user_string', $v1, $raw);
    }


    public function getSessionId() {
        return $this->getFieldValue('session_id');
    }

    public function setSessionId($v1, $raw=false) {
        $this->setFieldValue('session_id', $v1, $raw);
    }


    public function getDateStarted() {
        return $this->getFieldValue('date_started');
    }

    public function setDateStarted($v1, $raw=false) {
        $this->setFieldValue('date_started', $v1, $raw);
    }


    public function getDateLastAccessed() {
        return $this->getFieldValue('date_last_accessed');
    }

    public function setDateLastAccessed($v1, $raw=false) {
        $this->setFieldValue('date_last_accessed', $v1, $raw);
    }


    public function getSecret() {
        return $this->getFieldValue('secret');
    }

    public function setSecret($v1, $raw=false) {
        $this->setFieldValue('secret', $v1, $raw);
    }




}
