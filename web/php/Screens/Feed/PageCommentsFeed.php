<?php

namespace Wikidot\Screens\Feed;

use Ozone\Framework\Database\Criteria;
use Ozone\Framework\Ozone;
use Wikidot\DB\PagePeer;
use Wikidot\DB\ForumThreadPeer;
use Wikidot\DB\ForumPostPeer;
use Wikidot\Utils\FeedScreen;
use Wikidot\Utils\GlobalProperties;
use Wikidot\Utils\ProcessException;
use Wikijump\Models\User;

class PageCommentsFeed extends FeedScreen
{

    public function render($runData)
    {
        $site = $runData->getTemp("site");

        $pl = $runData->getParameterList();
        $pageId = $pl->getParameterValue("p");

        $parmHash = md5(serialize($pl->asArray()));

        $key = 'pagecommentsfeed_f..'.$site->getUnixName().'..'.$pageId.'..'.$parmHash;

        $akey = 'forumall_lc..'.$site->getUnixName();

        $mc = OZONE::$memcache;
        $struct = $mc->get($key);
        $allForumTimestamp = $mc->get($akey);
        if ($struct) {
            // check the times
            $cacheTimestamp = $struct['timestamp'];
            $threadId = $struct['threadId'];
            $tkey = 'forumthread_lc..'.$site->getUnixName().'..'.$threadId; // last change timestamp
            $changeTimestamp = $mc->get($tkey);
            if ($changeTimestamp && $changeTimestamp <= $cacheTimestamp && $allForumTimestamp && $allForumTimestamp <= $cacheTimestamp) {
                $runData->ajaxResponseAdd("threadId", $threadId);
                return $struct['content'];
            }
        }

        $out = parent::render($runData);

        // and store the data now
        $struct = array();
        $now = time();
        $struct['timestamp'] = $now;
        $struct['content'] = $out;
        $struct['threadId']=$this->threadId;

        if (!$changeTimestamp) {
            $tkey = 'forumthread_lc..'.$site->getUnixName().'..'.$this->threadId; // last change timestamp
            $changeTimestamp = $mc->get($tkey);
        }

        $mc->set($key, $struct, 0, 1000);
        if (!$changeTimestamp) {
            $tkey = 'forumthread_lc..'.$site->getUnixName().'..'.$this->threadId;
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
        $pageId = $pl->getParameterValue("p");

        $page = PagePeer::instance()->selectByPrimaryKey($pageId);
        $threadId = $page->getThreadId();

        $thread = ForumThreadPeer::instance()->selectByPrimaryKey($threadId);
        if ($thread == null) {
            throw new ProcessException("No such thread.", "no_thread");
        }
        $this->threadId = $thread->getThreadId();

        $channel = array();

        $channel['title'] = _('Comments for page').' "'.$page->getTitleOrUnixName().'"';
        $channel['link'] = GlobalProperties::$HTTP_SCHEMA . "://" . $site->getDomain()."/".$page->getUnixName()."/comments/show";

        $items = array();

        $c = new Criteria();
        $c->add("thread_id", $threadId);
        $c->add("forum_post.site_id", $site->getSiteId());
        $c->addJoin("user_id", "ozone_user.user_id");
        $c->addOrderDescending("post_id");
        $c->setLimit(20);
        $posts = ForumPostPeer::instance()->select($c);

        foreach ($posts as $post) {
            $item = array();

            if ($post->getTitle() != '') {
                $item['title'] = $post->getTitle();
            } else {
                $item['title'] = "(no title)";
            }
            $item['link'] = $channel['link'].'#post-'.$post->getPostId();
            $item['guid'] = $item['link'];
            $item['date'] = date('r', $post->getDatePosted()->getTimestamp());
            // TODO: replace relative links with absolute links!
            $content =  $post->getText();

            $content = preg_replace('/
                (<.*?)       # Opening tag and its name
                (src|href)   # Attribute selection
                =
                "\/([^"]+)"  # Value of attribute
                ([^>]*>)     # Any other attributes - these must come AFTER src/href
                /six', '\\1\\2="'.GlobalProperties::$HTTP_SCHEMA . "://" . $site->getDomain().'/\\3"\\4', $content);
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
