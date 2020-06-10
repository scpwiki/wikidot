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
 * Base class mapped to the database table site_settings.
 */
class SiteSettingsBase extends BaseDBObject {

    protected function internalInit(){
        $this->tableName='site_settings';
        $this->peerName = 'DB\\SiteSettingsPeer';
        $this->primaryKeyName = 'site_id';
        $this->fieldNames = array( 'site_id' ,  'allow_membership_by_apply' ,  'allow_membership_by_password' ,  'membership_password' ,  'private_landing_page' ,  'hide_navigation_unauthorized' ,  'max_private_members' ,  'max_private_viewers' ,  'ssl_mode' ,  'file_storage_size' ,  'max_upload_file_size' ,  'openid_enabled' ,  'allow_members_invite' ,  'enable_all_pingback_out' );

        //$this->fieldDefaultValues=
    }






    public function getSiteId() {
        return $this->getFieldValue('site_id');
    }

    public function setSiteId($v1, $raw=false) {
        $this->setFieldValue('site_id', $v1, $raw);
    }


    public function getAllowMembershipByApply() {
        return $this->getFieldValue('allow_membership_by_apply');
    }

    public function setAllowMembershipByApply($v1, $raw=false) {
        $this->setFieldValue('allow_membership_by_apply', $v1, $raw);
    }


    public function getAllowMembershipByPassword() {
        return $this->getFieldValue('allow_membership_by_password');
    }

    public function setAllowMembershipByPassword($v1, $raw=false) {
        $this->setFieldValue('allow_membership_by_password', $v1, $raw);
    }


    public function getMembershipPassword() {
        return $this->getFieldValue('membership_password');
    }

    public function setMembershipPassword($v1, $raw=false) {
        $this->setFieldValue('membership_password', $v1, $raw);
    }


    public function getPrivateLandingPage() {
        return $this->getFieldValue('private_landing_page');
    }

    public function setPrivateLandingPage($v1, $raw=false) {
        $this->setFieldValue('private_landing_page', $v1, $raw);
    }


    public function getHideNavigationUnauthorized() {
        return $this->getFieldValue('hide_navigation_unauthorized');
    }

    public function setHideNavigationUnauthorized($v1, $raw=false) {
        $this->setFieldValue('hide_navigation_unauthorized', $v1, $raw);
    }


    public function getMaxPrivateMembers() {
        return $this->getFieldValue('max_private_members');
    }

    public function setMaxPrivateMembers($v1, $raw=false) {
        $this->setFieldValue('max_private_members', $v1, $raw);
    }


    public function getMaxPrivateViewers() {
        return $this->getFieldValue('max_private_viewers');
    }

    public function setMaxPrivateViewers($v1, $raw=false) {
        $this->setFieldValue('max_private_viewers', $v1, $raw);
    }


    public function getSslMode() {
        return $this->getFieldValue('ssl_mode');
    }

    public function setSslMode($v1, $raw=false) {
        $this->setFieldValue('ssl_mode', $v1, $raw);
    }


    public function getFileStorageSize() {
        return $this->getFieldValue('file_storage_size');
    }

    public function setFileStorageSize($v1, $raw=false) {
        $this->setFieldValue('file_storage_size', $v1, $raw);
    }


    public function getMaxUploadFileSize() {
        return $this->getFieldValue('max_upload_file_size');
    }

    public function setMaxUploadFileSize($v1, $raw=false) {
        $this->setFieldValue('max_upload_file_size', $v1, $raw);
    }


    public function getOpenidEnabled() {
        return $this->getFieldValue('openid_enabled');
    }

    public function setOpenidEnabled($v1, $raw=false) {
        $this->setFieldValue('openid_enabled', $v1, $raw);
    }


    public function getAllowMembersInvite() {
        return $this->getFieldValue('allow_members_invite');
    }

    public function setAllowMembersInvite($v1, $raw=false) {
        $this->setFieldValue('allow_members_invite', $v1, $raw);
    }


    public function getEnableAllPingbackOut() {
        return $this->getFieldValue('enable_all_pingback_out');
    }

    public function setEnableAllPingbackOut($v1, $raw=false) {
        $this->setFieldValue('enable_all_pingback_out', $v1, $raw);
    }




}
