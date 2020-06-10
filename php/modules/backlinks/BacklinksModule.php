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


use DB\PagePeer;

class BacklinksModule extends SmartyModule {
    public function build($runData){

        $pageId = $runData->getParameterList()->getParameterValue("page_id");

        if(!$pageId || !is_numeric($pageId)){
            throw new ProcessException(_("The page can not be found or does not exist."), "no_page");
        }

        // create a very custom query ;-)
        $c = new Criteria();
        $q = "SELECT page_id, title, unix_name FROM page_link, page " .
                "WHERE page_link.to_page_id='".db_escape_string($pageId)."' " .
                "AND page_link.from_page_id=page.page_id ORDER BY COALESCE(title, unix_name)";

        $c->setExplicitQuery($q);

        $pages = PagePeer::instance()->select($c);

        $q = "SELECT page_id, title, unix_name FROM page, page_inclusion " .
                "WHERE page_inclusion.included_page_id='".db_escape_string($pageId)."' " .
                "AND page_inclusion.including_page_id=page.page_id ORDER BY COALESCE(title, unix_name)";

        $c->setExplicitQuery($q);

        $pagesI = PagePeer::instance()->select($c);

        $runData->contextAdd("pagesI",$pagesI);

        $runData->contextAdd("pages",$pages);
        $runData->contextAdd("pagesCount", count($pages));
    }
}
