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


use DB\CategoryPeer;
use DB\PagePeer;

class PagesListByTagModule extends SmartyModule {

    public function render($runData){
        $site = $runData->getTemp("site");
        $pl = $runData->getParameterList();
        $threadId = $pl->getParameterValue("t");

        $parmHash = md5(serialize($pl->asArray()));

        $key = 'list_pages_by_tags_v..'.$site->getSiteId().'..'.$parmHash;
        $tkey = 'page_tags_lc..'.$site->getSiteId(); // last change timestamp

        $mc = OZONE::$memcache;
        $struct = $mc->get($key);

        $cacheTimestamp = $struct['timestamp'];
        $changeTimestamp = $mc->get($tkey);

        if($struct){
            // check the times

            if($changeTimestamp && $changeTimestamp <= $cacheTimestamp){

                $out = $struct['content'];
                return $out;
            }
        }

        $out = parent::render($runData);

        // and store the data now
        $struct = array();
        $now = time();
        $struct['timestamp'] = $now;
        $struct['content'] = $out;

        $mc->set($key, $struct, 0, 120);

        if(!$changeTimestamp){
            $changeTimestamp = $now;
            $mc->set($tkey, $changeTimestamp, 0, 3600);
        }

        return $out;
    }

    public function build($runData){
        $pl = $runData->getParameterList();

        $site = $runData->getTemp("site");

        $tag = $pl->getParameterValue("tag");
        if($tag === null){
            $runData->setModuleTemplate("Empty");
            return '';
        }

        // get pages

        $categoryName =  $pl->getParameterValue("category");
        if($categoryName){
            $category = CategoryPeer::instance()->selectByName($categoryName, $site->getSiteId());
            if($category == null){
                return '';
            }
            $runData->contextAdd("category", $category);
        }

        $c = new Criteria();
        $c->setExplicitFrom("page, page_tag");
        $c->add("page_tag.tag", $tag);
        $c->add("page_tag.site_id", $site->getSiteId());
        $c->add("page_tag.page_id", "page.page_id", "=", false);
        if($category){
            $c->add("page.category_id", $category->getCategoryId());
        }
        $c->addOrderAscending('COALESCE(page.title, page.unix_name)');

        $pages = PagePeer::instance()->select($c);

    //    $q = "SELECT site.* FROM site, tag WHERE tag.tag = '".db_escape_string($tag")."'

        $runData->contextAdd("tag", $tag);
        $runData->contextAdd("pages", $pages);
        $runData->contextAdd("pageCount", count($pages));

        $runData->contextAdd("pageUnixName", $runData->getTemp("page")->getUnixName());

    }

}
