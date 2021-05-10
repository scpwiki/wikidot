<?php

namespace Wikidot\Modules\UserInfo;

use Ozone\Framework\Database\Criteria;

use Wikidot\DB\SitePeer;
use Wikidot\DB\PagePeer;
use Wikidot\Utils\SmartyLocalizedModule;
use Wikijump\Models\User;

class UserInfoProfileModule extends SmartyLocalizedModule
{

    public function build($runData)
    {
        $pl = $runData->getParameterList();
        $userId = $pl->getParameterValue("user_id");

        $user = User::find($userId);
        $runData->contextAdd("user", $user);

        $avatarUri = '/common--images/avatars/'.floor($userId/1000).'/'.$userId.'/a48.png';
        $runData->contextAdd("avatarUri", $avatarUri);

        $runData->contextAdd('karmaLevel', $user->karma_level);
    }
}
