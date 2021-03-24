<?php

namespace Wikidot\Modules\CreateAccount;


use Ozone\Framework\SmartyModule;

class CreateAccount3Module extends SmartyModule
{

    public function build($runData)
    {
        $user = $runData->getUser();

        $runData->contextAdd("user", $user);
    }
}
