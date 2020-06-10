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


use DB\ForumThreadPeer;
use DB\ForumGroupPeer;
use DB\ForumCategoryPeer;

class ForumThreadMoveModule extends SmartyModule {

    public function build($runData){
        $pl = $runData->getParameterList();

        $threadId = $pl->getParameterValue("threadId");
        $site = $runData->getTemp("site");

        $db = Database::connection();
        $db->begin();

        $thread = ForumThreadPeer::instance()->selectByPrimaryKey($threadId);
        if($thread == null || $thread->getSiteId() !== $site->getSiteId()){
            throw new ProcessException(_("No thread found... Is it deleted?"), "no_thread");
        }

        $category = $thread->getForumCategory();
        WDPermissionManager::instance()->hasForumPermission('moderate_forum', $runData->getUser(), $category);

        $runData->contextAdd("thread", $thread);
        $runData->contextAdd("category", $thread->getForumCategory());

        // and select categories to move into too.

        $c = new Criteria();
        $c->add("site_id", $site->getSiteId());
        $c->addOrderDescending("visible");
        $c->addOrderAscending("sort_index");

        $groups = ForumGroupPeer::instance()->select($c);

        $res = array();

        foreach($groups as $g){
            $c = new Criteria();
            $c->add("group_id", $g->getGroupId());

            $c->addOrderAscending("sort_index");

            $categories = ForumCategoryPeer::instance()->select($c);
            foreach($categories as $cat){
                $res[] = array('group' => $g, 'category' => $cat);
            }
        }

        $runData->contextAdd("categories", $res);

        $db->commit();
    }

}
