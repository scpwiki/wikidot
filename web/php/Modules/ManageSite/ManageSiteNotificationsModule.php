<?php

namespace Wikidot\Modules\ManageSite;

use Wikidot\Utils\ManageSiteBaseModule;

class ManageSiteNotificationsModule extends ManageSiteBaseModule
{

    public function build($runData)
    {
        $user = $runData->getUser();
        $username = $user->username;

        $password = $user->password;

        $password = substr($password, 0, 15);

        $runData->contextAdd("feedUsername", $username);
        $runData->contextAdd("feedPassword", $password);

        $runData->contextAdd("site", $runData->getTemp("site"));
    }
}
