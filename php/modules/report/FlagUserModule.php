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
 * @version $Id$
 * @copyright Copyright (c) 2008, Wikidot Inc.
 * @license http://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License
 */


use DB\OzoneUserPeer;
use DB\UserAbuseFlagPeer;

class FlagUserModule extends SmartyModule {

    public function isAllowed($runData){
        $userId = $runData->getUserId();
        if($userId == null || $userId <1){
            throw new WDPermissionException(_("This option is available only to registered (and logged-in) users."));
        }
        return true;
    }

    public function build($runData){
        $pl = $runData->getParameterList();

        $targetUserId = $pl->getParameterValue("targetUserId");
        if($targetUserId == null || $targetUserId == '' || !is_numeric($targetUserId)){
            throw new ProcessException(_("Error processing the request."), "no_target_user");
        }

        $targetUser = OzoneUserPeer::instance()->selectByPrimaryKey($targetUserId);
        if($targetUser == null){
            throw new ProcessException(_("Error processing the request."), "no_target_user");
        }

        $site = $runData->getTemp("site");
        $user = $runData->getUser();

        if($targetUser->getUserId() === $user->getUserId()){
            throw new ProcessException(_("Sorry, event with the extreme level of self-criticism you can not flag yourself as an abusive user ;-)") ,"not_yourself");
        }

        // check if flagged already
        $c = new Criteria();
        $c->add("user_id", $user->getUserId());
        $c->add("target_user_id", $targetUser->getUserId());

        $flag = UserAbuseFlagPeer::instance()->selectOne($c);

        if($flag){
            $runData->contextAdd("flagged", true);
        }

        $runData->contextAdd("user", $targetUser);

    }

}
