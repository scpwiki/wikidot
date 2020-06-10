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
 * Base class mapped to the database table category.
 */
class CategoryBase extends BaseDBObject {

    protected function internalInit(){
        $this->tableName='category';
        $this->peerName = 'DB\\CategoryPeer';
        $this->primaryKeyName = 'category_id';
        $this->fieldNames = array( 'category_id' ,  'site_id' ,  'name' ,  'theme_default' ,  'theme_id' ,  'theme_external_url' ,  'permissions_default' ,  'permissions' ,  'license_default' ,  'license_id' ,  'license_other' ,  'nav_default' ,  'top_bar_page_name' ,  'side_bar_page_name' ,  'template_id' ,  'per_page_discussion' ,  'per_page_discussion_default' ,  'rating' ,  'category_template_id' ,  'autonumerate' ,  'page_title_template' ,  'enable_pingback_out' ,  'enable_pingback_in' );

        //$this->fieldDefaultValues=
    }






    public function getCategoryId() {
        return $this->getFieldValue('category_id');
    }

    public function setCategoryId($v1, $raw=false) {
        $this->setFieldValue('category_id', $v1, $raw);
    }


    public function getSiteId() {
        return $this->getFieldValue('site_id');
    }

    public function setSiteId($v1, $raw=false) {
        $this->setFieldValue('site_id', $v1, $raw);
    }


    public function getName() {
        return $this->getFieldValue('name');
    }

    public function setName($v1, $raw=false) {
        $this->setFieldValue('name', $v1, $raw);
    }


    public function getThemeDefault() {
        return $this->getFieldValue('theme_default');
    }

    public function setThemeDefault($v1, $raw=false) {
        $this->setFieldValue('theme_default', $v1, $raw);
    }


    public function getThemeId() {
        return $this->getFieldValue('theme_id');
    }

    public function setThemeId($v1, $raw=false) {
        $this->setFieldValue('theme_id', $v1, $raw);
    }


    public function getThemeExternalUrl() {
        return $this->getFieldValue('theme_external_url');
    }

    public function setThemeExternalUrl($v1, $raw=false) {
        $this->setFieldValue('theme_external_url', $v1, $raw);
    }


    public function getPermissionsDefault() {
        return $this->getFieldValue('permissions_default');
    }

    public function setPermissionsDefault($v1, $raw=false) {
        $this->setFieldValue('permissions_default', $v1, $raw);
    }


    public function getPermissions() {
        return $this->getFieldValue('permissions');
    }

    public function setPermissions($v1, $raw=false) {
        $this->setFieldValue('permissions', $v1, $raw);
    }


    public function getLicenseDefault() {
        return $this->getFieldValue('license_default');
    }

    public function setLicenseDefault($v1, $raw=false) {
        $this->setFieldValue('license_default', $v1, $raw);
    }


    public function getLicenseId() {
        return $this->getFieldValue('license_id');
    }

    public function setLicenseId($v1, $raw=false) {
        $this->setFieldValue('license_id', $v1, $raw);
    }


    public function getLicenseOther() {
        return $this->getFieldValue('license_other');
    }

    public function setLicenseOther($v1, $raw=false) {
        $this->setFieldValue('license_other', $v1, $raw);
    }


    public function getNavDefault() {
        return $this->getFieldValue('nav_default');
    }

    public function setNavDefault($v1, $raw=false) {
        $this->setFieldValue('nav_default', $v1, $raw);
    }


    public function getTopBarPageName() {
        return $this->getFieldValue('top_bar_page_name');
    }

    public function setTopBarPageName($v1, $raw=false) {
        $this->setFieldValue('top_bar_page_name', $v1, $raw);
    }


    public function getSideBarPageName() {
        return $this->getFieldValue('side_bar_page_name');
    }

    public function setSideBarPageName($v1, $raw=false) {
        $this->setFieldValue('side_bar_page_name', $v1, $raw);
    }


    public function getTemplateId() {
        return $this->getFieldValue('template_id');
    }

    public function setTemplateId($v1, $raw=false) {
        $this->setFieldValue('template_id', $v1, $raw);
    }


    public function getPerPageDiscussion() {
        return $this->getFieldValue('per_page_discussion');
    }

    public function setPerPageDiscussion($v1, $raw=false) {
        $this->setFieldValue('per_page_discussion', $v1, $raw);
    }


    public function getPerPageDiscussionDefault() {
        return $this->getFieldValue('per_page_discussion_default');
    }

    public function setPerPageDiscussionDefault($v1, $raw=false) {
        $this->setFieldValue('per_page_discussion_default', $v1, $raw);
    }


    public function getRating() {
        return $this->getFieldValue('rating');
    }

    public function setRating($v1, $raw=false) {
        $this->setFieldValue('rating', $v1, $raw);
    }


    public function getCategoryTemplateId() {
        return $this->getFieldValue('category_template_id');
    }

    public function setCategoryTemplateId($v1, $raw=false) {
        $this->setFieldValue('category_template_id', $v1, $raw);
    }


    public function getAutonumerate() {
        return $this->getFieldValue('autonumerate');
    }

    public function setAutonumerate($v1, $raw=false) {
        $this->setFieldValue('autonumerate', $v1, $raw);
    }


    public function getPageTitleTemplate() {
        return $this->getFieldValue('page_title_template');
    }

    public function setPageTitleTemplate($v1, $raw=false) {
        $this->setFieldValue('page_title_template', $v1, $raw);
    }


    public function getEnablePingbackOut() {
        return $this->getFieldValue('enable_pingback_out');
    }

    public function setEnablePingbackOut($v1, $raw=false) {
        $this->setFieldValue('enable_pingback_out', $v1, $raw);
    }


    public function getEnablePingbackIn() {
        return $this->getFieldValue('enable_pingback_in');
    }

    public function setEnablePingbackIn($v1, $raw=false) {
        $this->setFieldValue('enable_pingback_in', $v1, $raw);
    }




}
