<?php


namespace Wikidot\Facade;

use \WDPermissionManager;
use Criteria;
use DB\PagePeer;
use DB\CategoryPeer;



class Site extends Base {
    /**
     * Get pages from a site
     *
     * Argument array keys:
     *  site: site to get pages from
     *  category: category to get pages from (optional)
     *
     * @param struct $args
     * @return struct
     */
    public function pages($args) {
        $this->parseArgs($args, array("performer", "site"));

        WDPermissionManager::instance()->canAccessSite($this->performer, $this->site);

        $c = new Criteria();
        $c->add("site_id", $this->site->getSiteId());

        if ($this->category) {
            $c->add("category_id", $this->category->getCategoryId());
        }

        $ret = array();
        foreach (PagePeer::instance()->selectByCriteria($c) as $page) {
            $ret[] = $this->repr($page, "meta");
        }
        return $ret;
    }

    /**
     * Get categories from a site
     *
     * Argument array keys:
     *  site: site to get categories from
     *
     * @param struct $args
     * @return struct
     */
    public function categories($args) {
        $this->parseArgs($args, array("performer", "site"));

        WDPermissionManager::instance()->canAccessSite($this->performer, $this->site);

        $c = new Criteria();
        $c->add("site_id", $this->site->getSiteId());

        $ret = array();
        foreach (CategoryPeer::instance()->selectByCriteria($c) as $category) {
            $ret[] = $this->repr($category);
        }
        return $ret;
    }
}
