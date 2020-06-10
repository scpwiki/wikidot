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


use DB\ForumGroupPeer;
use DB\ForumCategoryPeer;

class ForumRecentPostsModule extends CacheableModule {

    protected $timeOut = 300;

    public function build($runData){

        $site = $runData->getTemp("site");

        // get forum groups

        $c = new Criteria();
        $c->add("site_id", $site->getSiteId());
        $c->add("visible", true);
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

        $runData->contextAdd("cats", $res);
    }

}
