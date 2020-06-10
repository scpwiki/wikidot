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


use DB\PetitionCampaignPeer;

class ViewPetitionCampaignModule extends SmartyModule {

    public function isAllowed($runData){
        WDPermissionManager::instance()->hasPermission('manage_site', $runData->getUser(), $runData->getTemp("site"));
        return true;
    }

    public function build($runData){
        $pl = $runData->getParameterList();
        $site = $runData->getTemp("site");
        // get the petition campaign...
        $campaignId = $pl->getParameterValue("campaignId");

        $c = new Criteria();
        $c->add("site_id", $site->getSiteId());
        $c->add("deleted", false);
        $c->add("campaign_id", $campaignId);

        $camp = PetitionCampaignPeer::instance()->selectOne($c);

        if(!$camp){
            throw new ProcessException(_("The campaign can not be found."));
        }

        $runData->contextAdd("campaign", $camp);

        // get all campaigns
        $c = new Criteria();
        $c->add("site_id", $site->getSiteId());
        $c->add("deleted", false);
        $camps = PetitionCampaignPeer::instance()->select($c);

        $runData->contextAdd("campaigns", $camps);
        $runData->contextAdd("campaignsCount", count($camps));

    }

}
