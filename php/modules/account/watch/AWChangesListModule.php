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


use DB\PageRevisionPeer;

class AWChangesListModule extends AccountBaseModule {

    public function build($runData){

        $user = $runData->getUser();

        $pl = $runData->getParameterList();

        $pageNumber = $pl->getParameterValue("page");
        if($pageNumber === null){
            $pageNumber = 1;
        }
        $limit = $pl->getParameterValue("limit");

        if($limit == null || !is_numeric($limit)){
            $limit = 20;
        }
        $perPage = $limit;
        $offset = ($pageNumber - 1)*$perPage;
        $count = $perPage*2 + 1;

        // join the tables:
        // watched_page, page_revision, user, page, site. ???

        $c = new Criteria();

        $c->addJoin("page_id", "page.page_id");
        $c->addJoin("page_id", "watched_page.page_id");
        $c->addJoin("user_id", "ozone_user.user_id");
        $c->add("watched_page.user_id", $user->getUserId());
        $c->addOrderDescending("page_revision.revision_id");
        $c->setLimit($count, $offset);

        $revisions = PageRevisionPeer::instance()->select($c);

        $counted = count($revisions);
        $pagerData = array();
        $pagerData['currentPage'] = $pageNumber;
        if($counted >$perPage*2){
            $knownPages=$pageNumber + 2;
            $pagerData['knownPages'] = $knownPages;
        }elseif($counted >$perPage){
            $knownPages=$pageNumber + 1;
            $pagerData['totalPages'] = $knownPages;
        } else {
            $totalPages = $pageNumber;
            $pagerData['totalPages'] = $totalPages;
        }
        $revisions = array_slice($revisions, 0, $perPage);

        $runData->contextAdd("pagerData", $pagerData);

        $runData->contextAdd("revisions", $revisions);

    }

}
