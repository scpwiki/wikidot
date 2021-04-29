<?php

namespace Wikidot\Screens\Feed;

use Ozone\Framework\Database\Criteria;
use Ozone\Framework\Ozone;
use Wikidot\DB\ForumThreadPeer;
use Wikidot\Utils\FeedScreen;
use Wikidot\Utils\GlobalProperties;
use Wikijump\Models\User;

class ForumThreadsFeed extends FeedScreen
{

    public function render($runData)
    {
        $site = $runData->getTemp("site");

        $pl = $runData->getParameterList();
        $parmHash = md5(serialize($pl->asArray()));

        $key = 'forumposts_f..'.$site->getUnixName().'..'.$parmHash;
        $tkey = 'forumstart_lc..'.$site->getUnixName(); // last change timestamp
        $akey = 'forumall_lc..'.$site->getUnixName();

        $mc = OZONE::$memcache;
        $struct = $mc->get($key);
        $changeTimestamp = $mc->get($tkey);
        $allForumTimestamp = $mc->get($akey);
        if ($struct) {
            // check the times
            $cacheTimestamp = $struct['timestamp'];

            // afford 1 minute delay
            if ($changeTimestamp && $changeTimestamp <= $cacheTimestamp+60 && $allForumTimestamp && $allForumTimestamp <= $cacheTimestamp) {
                return $struct['content'];
            }
        }

        $out = parent::render($runData);

        // and store the data now
        $struct = array();
        $now = time();
        $struct['timestamp'] = $now;
        $struct['content'] = $out;

        $mc->set($key, $struct, 0, 1000);
        if (!$changeTimestamp) {
            $changeTimestamp = $now;
            $mc->set($tkey, $changeTimestamp, 0, 1000);
        }
        if (!$allForumTimestamp) {
            $allForumTimestamp = $now;
            $mc->set($akey, $allForumTimestamp, 0, 10000);
        }

        return $out;
    }

    public function build($runData)
    {

        $site = $runData->getTemp("site");

        $pl = $runData->getParameterList();
        $categoryId = $pl->getParameterValue("c");

        $channel = array();

        $channel['title'] =  $site->getName()." - "._("new forum threads");
        $channel['link'] = GlobalProperties::$HTTP_SCHEMA . "://" . $site->getDomain()."/forum/start";
        $channel['description'] = _("Threads in forums of the site")." \"".$site->getName()."\"";
        if ($site->getSubtitle()) {
            $channel['description'] .=  " - ".$site->getSubtitle();
        }

        $items = array();

        $c = new Criteria();
        $c->add("forum_thread.site_id", $site->getSiteId());
        $c->add("forum_group.visible", true);
        $c->addJoin("user_id", "ozone_user.user_id");
        $c->addJoin("forum_thread.category_id", "forum_category.category_id");
        $c->addJoin("forum_category.group_id", "forum_group.group_id");
        $c->addOrderDescending("thread_id");
        $c->setLimit(20);
        $threads = ForumThreadPeer::instance()->select($c);

        foreach ($threads as $thread) {
            $item = array();

            $item['title'] = $thread->getTitle();
            $item['link'] = GlobalProperties::$HTTP_SCHEMA . "://" . $site->getDomain()."/forum/t-".$thread->getThreadId().'/'.$thread->getUnixifiedTitle();
            $item['guid'] = GlobalProperties::$HTTP_SCHEMA . "://" . $site->getDomain()."/forum/t-".$thread->getThreadId();
            $item['date'] = date('r', $thread->getDateStarted()->getTimestamp());

            //replace relative links with absolute links!
            $post = $thread->getFirstPost();
            if (!$post) {
                continue;
            }
            $content =  $post->getText();

            $content = preg_replace(
                '/
                (<.*?)       # Opening tag and its name
                (src|href)   # Attribute selection
                =
                "\/([^"]+)"  # Value of attribute
                ([^>]*>)     # Any other attributes - these must come AFTER src/href
                /six',
                '\\1\\2="'.GlobalProperties::$HTTP_SCHEMA . "://" . $site->getDomain().'/\\3"\\4',
                $content
            );
            # Remove any script elements
            $content = preg_replace(
                '/
                <script\s+[^>]+>
                .*?
                <\/script>
                /isx',
                '',
                $content
            );
            # Strip out any attribute that starts with "on" from any element
            $content = preg_replace(
                '/
                (<[^>]*\s+)
                on[a-z]+="[^"]+"
                ([^>]*>)
                /six',
                '\\1 \\2',
                $content
            );

            if ($thread->getDescription()) {
                $item['description'] = $thread->getDescription();
            }

            $item['content'] = $content;
            if ($post->getUserId() != User::ANONYMOUS_USER && $post->getUserId() != User::AUTOMATIC_USER) {
                $item['authorUserId'] = $post->getUserId();
                $user = $post->getUser();
                $item['author']=$user->getNickName();
            } else {
                $item['author']=$post->getUserString();
            }
            $items[] = $item;
        }

        $runData->contextAdd("channel", $channel);
        $runData->contextAdd("items", $items);
    }
}
