<?php

namespace Wikidot\Modules\ManageSite\Elists;

use Ozone\Framework\Database\Criteria;
use Wikidot\DB\EmailListPeer;
use Wikidot\Utils\ManageSiteBaseModule;

class ManageSiteEmailListsModule extends ManageSiteBaseModule
{

    public function build($runData)
    {
        $site = $runData->getTemp('site');

        // get all email lists.

        $c = new Criteria();
        $c->add('site_id', $site->getSiteId());
        $c->addOrderDescending('special');
        $c->addOrderAscending('title');

        $lists = EmailListPeer::instance()->select($c);

        $runData->contextAdd('lists', $lists);
        $runData->contextAdd('site', $site);
    }
}
