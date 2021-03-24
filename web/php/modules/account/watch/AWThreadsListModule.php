<?php

namespace Wikidot\Modules\Account\Watch;




use Ozone\Framework\Database\Criteria;
use Wikidot\DB\ForumThreadPeer;
use Wikidot\Utils\AccountBaseModule;

class AWThreadsListModule extends AccountBaseModule
{

    public function build($runData)
    {

        $user = $runData->getUser();
        $runData->contextAdd("user", $user);

        $pl = $runData->getParameterList();

        // get watched threads for this user

        $c = new Criteria();
        /*$c->add("watched_forum_thread.user_id", $user->getUserId());
        $c->addJoin("thread_id", "forum_thread.thread_id");
        $c->addOrderAscending("watched_id");
        */
        /*
        $c->setExplicitFrom("forum_thread, watched_forum_thread");
        $c->add("watched_forum_thread.user_id", $user->getUserId());
        $c->
        */

        $q = "SELECT forum_thread.* FROM watched_forum_thread, forum_thread " .
                "WHERE watched_forum_thread.user_id='".$user->getUserId()."' " .
                        "AND watched_forum_thread.thread_id=forum_thread.thread_id";
        $c->setExplicitQuery($q);

        $threads = ForumThreadPeer::instance()->select($c);

        $runData->contextAdd("threads", $threads);

        $runData->contextAdd("threadsCount", count($threads));
    }
}
