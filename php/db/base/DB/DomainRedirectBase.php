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
 * Base class mapped to the database table domain_redirect.
 */
class DomainRedirectBase extends BaseDBObject {

    protected function internalInit(){
        $this->tableName='domain_redirect';
        $this->peerName = 'DB\\DomainRedirectPeer';
        $this->primaryKeyName = 'redirect_id';
        $this->fieldNames = array( 'redirect_id' ,  'site_id' ,  'url' );

        //$this->fieldDefaultValues=
    }






    public function getRedirectId() {
        return $this->getFieldValue('redirect_id');
    }

    public function setRedirectId($v1, $raw=false) {
        $this->setFieldValue('redirect_id', $v1, $raw);
    }


    public function getSiteId() {
        return $this->getFieldValue('site_id');
    }

    public function setSiteId($v1, $raw=false) {
        $this->setFieldValue('site_id', $v1, $raw);
    }


    public function getUrl() {
        return $this->getFieldValue('url');
    }

    public function setUrl($v1, $raw=false) {
        $this->setFieldValue('url', $v1, $raw);
    }




}
