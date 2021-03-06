<?php

namespace Wikidot\Modules\Account\PM;




use Ozone\Framework\Database\Criteria;
use Wikidot\DB\PrivateMessagePeer;
use Wikidot\Utils\AccountBaseModule;

class PMInboxModule extends AccountBaseModule
{

    public function build($runData)
    {

        $userId = $runData->getUserId();

        $pl = $runData->getParameterList();

        $pageNo = $pl->getParameterValue("page");
        if ($pageNo == null || $pageNo<0) {
            $pageNo = 1;
        }

        // get inbox messages for the user
        $c = new Criteria();
        $c->add("to_user_id", $userId);
        $c->add("flag", 0); // for inbox

        // also count them all!
        $co = PrivateMessagePeer::instance()->selectCount($c);

        $c->addOrderDescending("message_id");

        $perPage = 30;
        // limits...
        $totalPages = ceil($co/$perPage);
        if ($pageNo>$totalPages) {
            $pageNo = $totalPages;
        }
        $offset = max(($pageNo-1) * $perPage, 0);

        $c->setLimit($perPage, $offset);
        $runData->contextAdd("totalPages", $totalPages);
        $runData->contextAdd("currentPage", $pageNo);

        $messages = PrivateMessagePeer::instance()->select($c);

        $runData->contextAdd("count", $co);
        $runData->contextAdd("totalPages", $totalPages);
        $runData->contextAdd("messages", $messages);
    }
}
