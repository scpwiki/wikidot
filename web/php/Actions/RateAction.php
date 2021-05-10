<?php

namespace Wikidot\Actions;
use Ozone\Framework\Database\Criteria;
use Ozone\Framework\Database\Database;
use Ozone\Framework\ODate;
use Ozone\Framework\SmartyAction;
use Wikidot\DB\Page;
use Wikidot\DB\PagePeer;
use Wikidot\DB\PageRateVotePeer;
use Wikidot\DB\PageRateVote;
use Wikidot\DB\MemberPeer;
use Wikidot\Utils\Outdater;
use Wikidot\Utils\ProcessException;

class RateAction extends SmartyAction
{

    private $message;

    public function perform($r)
    {
    }

    public function ratePageEvent($runData)
    {
        $pl = $runData->getParameterList();
        $site = $runData->getTemp("site");

        $pageId = $pl->getParameterValue("pageId");
        $user = $runData->getUser();

        $points = $pl->getParameterValue("points");

        if ($points > 5 || $points < -1) {
            throw new ProcessException("Error");
        }

        /**
         * @var Page|null $page
         */
        $page = PagePeer::instance()->selectByPrimaryKey($pageId);
        if ($page == null || $page->getSiteId() != $runData->getTemp("site")->getSiteId()) {
            throw new ProcessException(_("Error getting page information."), "no_page");
        }

        // check if allowed
        if (!$this->canRatePage($user, $page)) {
            // prepare the message

            throw new ProcessException($this->message);
        }

        // ok, now check if not rated already...

        $c = new Criteria();
        $c->add("page_id", $page->getPageId());
        $c->add("user_id", $user->id);
        if ($pl->getParameterValue("force")) {
            $v = PageRateVotePeer::instance()->selectOne($c);
            PageRateVotePeer::instance()->delete($c);
            $rpoints = $points - $v->getRate();
        } else {
            $v = PageRateVotePeer::instance()->selectOne($c);
            if ($v) {
                $runData->ajaxResponseAdd("status", "already_voted");
                $runData->setModuleTemplate("PageRate/AlreadyRatedModule");
                $runData->contextAdd("points", $points);
                $runData->contextAdd("rate", $v->getRate());
                return;
                //throw new ProcessException("It seems you have already voted here. " .
            }
            $rpoints = $points;
        }

        //ok, now VOTEEEEeeeeee!!!!!

        $db = Database::connection();
        $db->begin();

        $v = new PageRateVote();
        $v->setUserId($user->id);
        $v->setPageId($page->getPageId());
        $v->setRate($points);
        $v->setDate(new ODate());

        $v->save();

        $category = $page->getCategory();
        if ($category->getRatingType() === "S") {
            // update page points
            $q = "UPDATE page SET rate=COALESCE((" .
                "SELECT round(avg(rate),2) FROM page_rate_vote WHERE page_id = '" . $page->getPageId() . "'),0) " .
                "WHERE page_id='" . $page->getPageId() . "'";
        } else {
            $q = "UPDATE page SET rate=COALESCE((" .
                "SELECT sum(rate) FROM page_rate_vote WHERE page_id = '".$page->getPageId()."'),0) " .
                "WHERE page_id='".$page->getPageId()."'";
        }

        $db->query($q);
        $outdater = new Outdater();
        $outdater->pageEvent("page_vote", $page);

        $db->commit();
        $runData->ajaxResponseAdd("type", $category->getRatingType());
        $c = new Criteria();
        $c->add("page_id", $page->getPageId());
        $v = PageRateVotePeer::instance()->selectOne($c);
        $runData->ajaxResponseAdd("votes", count($v));
        $runData->ajaxResponseAdd("points", $rpoints);
    }

    public function cancelVoteEvent($runData)
    {
        $pl = $runData->getParameterList();
        $site = $runData->getTemp("site");

        $pageId = $pl->getParameterValue("pageId");
        $user = $runData->getUser();

        $page = PagePeer::instance()->selectByPrimaryKey($pageId);
        if (!$pageId || $page == null || $page->getSiteId() != $runData->getTemp("site")->getSiteId()) {
            throw new ProcessException(_("Error getting page information."), "no_page");
        }

        // check if allowed
        if (!$this->canRatePage($user, $page)) {
            // prepare the message

            throw new ProcessException($this->message);
        }

        $db = Database::connection();
        $db->begin();

        $c = new Criteria();
        $c->add("page_id", $page->getPageId());
        $c->add("user_id", $user->id);
        $v = PageRateVotePeer::instance()->selectOne($c);
        if (!$v) {
            $runData->ajaxResponseAdd("points", 0);
            return;
        }
        PageRateVotePeer::instance()->delete($c);
        $rpoints = 0 - $v->getRate();

        $category = $page->getCategory();
        if ($category->getRatingType() === "S") {
            // update page points
            $q = "UPDATE page SET rate=COALESCE((" .
                "SELECT round(avg(rate),2) FROM page_rate_vote WHERE page_id = '" . $page->getPageId() . "'),0) " .
                "WHERE page_id='" . $page->getPageId() . "'";
        } else {
            $q = "UPDATE page SET rate=COALESCE((" .
                "SELECT sum(rate) FROM page_rate_vote WHERE page_id = '".$page->getPageId()."'),0) " .
                "WHERE page_id='".$page->getPageId()."'";
        }

        $db->query($q);
        $outdater = new Outdater();
        $outdater->pageEvent("page_vote", $page);

        $db->commit();
        $runData->ajaxResponseAdd("type", $category->getRatingType());
        $c = new Criteria();
        $c->add("page_id", $page->getPageId());
        $v = PageRateVotePeer::instance()->selectOne($c);
        $runData->ajaxResponseAdd("votes", count($v));
        $runData->ajaxResponseAdd("points", $rpoints);
    }

    private function canRatePage($user, $page)
    {
        if (!$user) {
            $this->message = _("You should be at least logged in to try rating...");
            return false;
        }

        $category = $page->getCategory();
        if ($category->getRatingEnabledEff()) {
            $ps = $category->getRatingBy();
            if ($ps == 'r') {
                return true;
            }

            if ($ps == 'm') {
                $c = new Criteria();
                $c->add("site_id", $category->getSiteId());
                $c->add("user_id", $user->id);
                $rel = MemberPeer::instance()->selectOne($c);
                if ($rel) {
                    return true;
                } else {
                    $this->message = _("Voting is enabled only for Site Members.");
                }
            }
        } else {
            $this->message = _("Rating is disabled for this page.");
        }
        return false;
    }
}
