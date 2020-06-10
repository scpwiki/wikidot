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
 * @package Ozone_Acl
 * @version $Id$
 * @copyright Copyright (c) 2008, Wikidot Inc.
 * @license http://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License
 */



use DB\OzoneUserPeer;
use DB\OzoneGroupPeer;
use DB\OzonePermissionPeer;
use DB\OzoneUserGroupRelation;
use DB\OzoneUserGroupRelationPeer;
use DB\OzoneGroup;

/**
 * Security (ACL) manager.
 *
 */
class SecurityManager {

    public static $sm;

    public static function instance(){
        if(self::$sm == null){
            self::$sm = new SecurityManager();
        }
        return self::$sm;
    }

    public  function getUserByID($userId){
        $peer = OzoneUserPeer::instance();
        $query = "WHERE user_id = '".db_escape_string($userId)."'";
        return $peer->selectOneByExplicitQuery($query);
    }

    public function getUserByName($username){
        $peer = OzoneUserPeer::instance();
        $query = "WHERE lower(name) = '".db_escape_string(strtolower($username))."'";
        return $peer->selectOneByExplicitQuery($query);
    }

    public function getUserByNickname($username){
        $peer = OzoneUserPeer::instance();
        $query = "WHERE lower(nick_name) = '".db_escape_string(strtolower($username))."'";
        return $peer->selectOneByExplicitQuery($query);
    }

    public function getUserByEmail($email){
        $peer = OzoneUserPeer::instance();
        $query = "WHERE email = '".db_escape_string($email)."'";
        return $peer->selectOneByExplicitQuery($query);
    }

    public function getGroupById($groupId){
        $peer = OzoneGroupPeer::instance();
        $query = "WHERE group_id = '".db_escape_string($groupId)."'";
        return $peer->selectOneByExplicitQuery($query);
    }

    public function getGroupByName($groupname){
        $peer = OzoneGroupPeer::instance();
        $query = "WHERE name = '".db_escape_string($groupname)."'";
        return $peer->selectOneByExplicitQuery($query);
    }

    public function authenticateUser($username, $password){
        // A slight digression here on dumb conventions.
        // The getUserByName method actually checks the *email* of the user.
        // Theoretically 'name' and 'email' are two different fields in the DB.
        // But if there's a way to have 'name' be something other than an email, I don't know how.
        // So if you want to look up by their friendly name/username, use the `nick_name` column.
        // TODO: Clean up this behavior everywhere.
        if(strpos($username, '@') !== false) { // Email provided
            $user = $this->getUserByEmail($username);
            if(password_verify($password, $user->getPassword())) {
                return $user;
            }
        }
        else { // No @, so it's a username.
            $user = $this->getUserByNickname($username);
            if(password_verify($password, $user->getPassword())) {
                return $user;
            }
        }
        return null;
    }

    public function setUserPassword($user, $password){
        if(gettype($user) == "string"){
            //get object
            $userObject = $this->getUserByName($user);
        } else {
            $userObject = $user;
        }
        $userObject->setPassword($password);
        $userObject->save();
    }

    public function getPermissionById($permissionId){
        $peer = OzonePermissionPeer::instance();
        $query = "WHERE permission_id = '".db_escape_string($permissionId)."'";
        return $peer->selectOneByExplicitQuery($query);
    }

    public function getPermissionByName($permissionname){
        $peer = OzonePermissionPeer::instance();
        $query = "WHERE name = '".db_escape_string($permissionname)."'";
        return $peer->selectOneByExplicitQuery($query);
    }

    public  function addUserToGroup($user, $group){
        if(gettype($user) == "string"){
            //get object
            $userObject = $this->getUserByName($user);
        } else {
            $userObject = $user;
        }

        if(gettype($group) == "string"){
            //get object
            $groupObject = $this->getGroupByName($group);
        } else {
            $groupObject = $group;
        }

        $relation = new OzoneUserGroupRelation();
        $relation->setOzoneUser($userObject);
        $relation->setOzoneGroup($groupObject);
        $relation->save();
    }

    public function delUserFromGroup($user, $group){
        if(gettype($user) == "string"){
            //get object
            $userObject = $this->getUserByName($user);
        } else {
            $userObject = $user;
        }

        if(gettype($group) == "string"){
            //get object
            $groupObject = $this->getGroupByName($group);
        } else {
            $groupObject = $group;
        }

        $c = new Criteria();
        $c->add("user_id", $userObject->getUserId());
        $c->add("group_id", $groupObject->getGroupId());
        OzoneUserGroupRelationPeer::instance()->delete($c);
    }

    public function createGroup($name, $parentGroup=null){
        if($parentGroup != null){
            if(gettype($parentGroup) == "string"){
                //    get object
                $parentGroupObject = $this->getGroupByName($parentGroup);
            } else {
                $parentGroupObject = $this->getGroupById($parentGroup);
            }
        }

        $newGroup = new OzoneGroup();
        $newGroup->setName("$name");
        if($parentGroupObject != null){
            $newGroup->setParentGroupId($parentGroupObject->getGroupId());
        }
        $newGroup->save();

    }

    /**
     * Checks if the user is a 'direct' member of the group - not taking
     * parent groups into account.
     */
    public function isUserInGroupDirectly($user, $group){
        if(gettype($user) == "string"){
            //get object
            $userObject = $this->getUserByName($user);
        } else {
            $userObject = $user;
        }

        if(gettype($group) == "string"){
            //get object
            $groupObject = $this->getGroupByName($group);
        } else {
            $groupObject = $group;
        }

        // now find the relation object between user and group
        $c = new Criteria();
        $c->add("user_id", $userObject->getUserId());
        $c->add("group_id", $groupObject->getGroupId());
        $rel = OzoneUserGroupRelationPeer::instance()->selectOne($c);

        if($rel !== null){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if the user is effectively a member of the group.
     */
    public function isUserInGroup($user, $group){
        if(gettype($user) == "string"){
            //get object
            $userObject = $this->getUserByName($user);
        } else {
            $userObject = $user;
        }

        if(gettype($group) == "string"){
            //get object
            $groupObject = $this->getGroupByName($group);
        } else {
            $groupObject = $group;
        }

        if($this->isUserInGroupDirectly($userObject, $groupObject)){
            return true;
        }

        $ancestors = $this->getGroupDescendants($groupObject);

        if($ancestors !== null){
            foreach($ancestors as $an){
                if($this->isUserInGroupDirectly($userObject, $an)){
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns (direct) children of the given group.
     */
    public function getGroupChildren($group){
        if(gettype($group) == "string"){
            //get object
            $groupObject = $this->getGroupByName($group);
        } else {
            $groupObject = $group;
        }

        $c = new Criteria();
        $c->add("parent_group_id", $groupObject->getGroupId());
        $groups = OzoneGroupPeer::instance()->select($c);
        return $groups;
    }

    /**
     * Returns all descendants of the given group.
     */
    public function getGroupDescendants($group){
        if(gettype($group) == "string"){
            //get object
            $groupObject = $this->getGroupByName($group);
        } else {
            $groupObject = $group;
        }

        $outarray = array();
        $tmparray2 = array();

        $tmparray1=$this->getGroupChildren($groupObject);
        while($tmparray1 != null && count($tmparray1)>0){
            foreach($tmparray1 as $g1){
                $tchildren = $this->getGroupChildren($g1);
                if($tchildren != null){
                    $tmparray2 = array_merge($tmparray2, $tchildren);
                }
            }
            $outarray = array_merge($outarray, $tmparray1);
            $tmparray1 = $tmparray2;
            $tmparray2 = array();
        }
        return $outarray;

    }

    /**
     * Returns the parrent group for the givent group.
     */
    public function getGroupParent($group){
        if(gettype($group) == "string"){
            //get object
            $groupObject = $this->getGroupByName($group);
        } else {
            $groupObject = $group;
        }
        $c = new Criteria();
        $c->add("group_id", $groupObject->getParentGroupId());
        $groups = OzoneGroupPeer::instance()->selectOne($c);
        return $group;

    }

    /**
     * Returns all ancestors of the group.
     */
    public function getGroupAncestors($group){
        if(gettype($group) == "string"){
            //get object
            $groupObject = $this->getGroupByName($group);
        } else {
            $groupObject = $group;
        }

        $out = array();
        $parentId = $groupObject->getParentGroupId();
        while($parentId != null){
            $parent = $this->getGroupById($parentId);
            $out[] = $parent;
            $parentId = $parent->getParentGroupId();
        }
        return $out;
    }

    public function getGroupMembers($group){
        if(gettype($group) == "string"){
            //get object
            $groupObject = $this->getGroupByName($group);
        } else {
            $groupObject = $group;
        }
        $c = new Criteria();
        $c->setExplicitFrom("ozone_user, ozone_user_group_relation");
        $c->setExplicitFields("ozone_user.*");
        $c->add("ozone_user_group_relation.group_id", $groupObject->getGroupId());
        $c->add("ozone_user_group_relation.user_id", "ozone_user.user_id", "=", false);

        $users = OzoneUserPeer::instance()->select($c);
        return $users;

    }

    public  function getUserPermissionSet($userId){

    }

    public  function hasUserPermission($user, $permission){

    }

    public  function getUserAllGroups($userId){
        $c = new Criteria();
        $c->add("user_id", $userId);
        $rels = OzoneUserGroupRelationPeer::instance()->select($c);
        // now get groups.
        $groups = array();
        foreach($rels as $rel){
            $c = new Criteria();
            $c->add("group_id", $rel->getGroupId());
            $group = OzoneGroupPeer::instance()->selectOne($c);
            $groups[$group->getGroupId()] = $group;
        }

        // now find all ancestors.
        foreach($groups as $g){
            $ancs = $this->getGroupAncestors($g);
            foreach($ancs as $a){
                $groups[$a->getGroupId()] = $a;
            }
        }

        return $groups;
    }

    public  function getGroupFinalPermissions($groupId){

    }

}
