<?php

namespace Wikidot\Modules\Wiki\Invitations;


use Ozone\Framework\Database\Criteria;

use Wikidot\DB\MemberPeer;
use Wikidot\DB\MembershipLinkPeer;

use Ozone\Framework\SmartyModule;
use Wikidot\Utils\ProcessException;
use Wikijump\Models\User;

class WhoInvitedResultsModule extends SmartyModule
{

    public function build($runData)
    {

        $site = $runData->getTemp("site");

        $pl = $runData->getParameterList();

        $userId = $pl->getParameterValue("userId");
        $user = User::find($userId);

        if (!$user) {
            throw new ProcessException(_("Invalid user."));
        }
        $c = new Criteria();
        $c->add("user_id", $userId);
        $c->add("site_id", $site->getSiteId());
        $mem = MemberPeer::instance()->selectOne($c);

        if (!$mem) {
            throw new ProcessException(_("The user is not a Member of this Wiki."));
        }

        $link = MembershipLinkPeer::instance()->selectByUserId($site->getSiteId(), $userId);
        if (!$link) {
            $runData->contextAdd("noData", true);
        } else {
            $chain = [];
            $chain[] = ['user' => $user, 'link' => $link];
            if ($link->getByUserId()) {
                do {
                    // get "parent"
                    // get link for the user
                    $user = User::find($link->getByUserId());
                    $link = MembershipLinkPeer::instance()->selectByUserId($site->getSiteId(), $user->id);
                    $chain[] = ['user' => $user, 'link' => $link];
                } while ($user && $link && $link->getByUserId());
            }
            $runData->contextAdd("chain", array_reverse($chain));
        }
    }
}
