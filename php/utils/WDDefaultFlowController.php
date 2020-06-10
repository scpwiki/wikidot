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


use DB\SitePeer;

class WDDefaultFlowController extends WebFlowController {

    public function process() {
        global $timeStart;

        // initialize logging service
        $logger = OzoneLogger::instance();
        $loggerFileOutput = new OzoneLoggerFileOutput();
        $loggerFileOutput->setLogFileName(WIKIDOT_ROOT."/logs/ozone.log");
        $logger->addLoggerOutput($loggerFileOutput);
        $logger->setDebugLevel(GlobalProperties::$LOGGER_LEVEL);

        $logger->debug("request processing started, logger initialized");

        Ozone ::init();

        $runData = new RunData();
        $runData->init();
        Ozone :: setRunData($runData);
        $logger->debug("RunData object created and initialized");

        // check if site (wiki) exists!
        $siteHost = $_SERVER["HTTP_HOST"];

        $memcache = \Ozone::$memcache;
        if(preg_match("/^([a-zA-Z0-9\-]+)\." . GlobalProperties::$URL_DOMAIN_PREG . "$/", $siteHost, $matches)==1){
            $siteUnixName=$matches[1];
            // select site based on the unix name

            // check memcached first!

            // the memcache block is to avoid database connection if possible

            $mcKey = 'site..'.$siteUnixName;
            $site = $memcache->get($mcKey);
            if($site == false){
                $c = new Criteria();
                $c->add("unix_name", $siteUnixName);
                $c->add("site.deleted", false);
                $site = SitePeer::instance()->selectOne($c);
                $memcache->set($mcKey, $site, 0, 3600);
            }
        } else {
            // select site based on the custom domain
            $mcKey = 'site_cd..'.$siteHost;
            $site = $memcache->get($mcKey);
            if($site == false){
                $c = new Criteria();
                $c->add("custom_domain", $siteHost);
                $c->add("site.deleted", false);
                $site = SitePeer::instance()->selectOne($c);
                $memcache->set($mcKey, $site, 0, 3600);
            }
            GlobalProperties::$SESSION_COOKIE_DOMAIN = '.'.$siteHost;

        }

        if($site == null){
            $runData->setScreenTemplate("wiki/SiteNotFound");
            exit(1);
        } else {
            $runData->setTemp("site", $site);
            //nasty global thing...
            $GLOBALS['siteId'] = $site->getSiteId();
            $GLOBALS['site'] = $site;
        }

        // set language
            $runData->setLanguage($site->getLanguage());
            $GLOBALS['lang'] = $site->getLanguage();

            // and for gettext too:

            $lang = $site->getLanguage();

            switch($lang){
                case 'pl':
                    $glang="pl_PL";
                    break;
                case 'en':
                    $glang="en_US";
                    break;
            }

            putenv("LANG=$glang");
            putenv("LANGUAGE=$glang");
            setlocale(LC_ALL, $glang.'.UTF-8');

            // Set the text domain as 'messages'
            $gdomain = 'messages';
            bindtextdomain($gdomain, WIKIDOT_ROOT.'/locale');
            textdomain($gdomain);

            $settings = $site->getSettings();
            // handle SSL
            $sslMode = $settings->getSslMode();

            if($_SERVER['HTTPS']){
                if(!$sslMode){
                    // not enabled, issue an errorr
                    throw new ProcessException(_("Secure access is not enabled for this Wiki."));
                }elseif($sslMode == "ssl_only_paranoid"){
                    // use secure authentication cookie
                    // i.e. change authentication scheme
                    GlobalProperties::$SESSION_COOKIE_NAME = "WIKIDOT_SESSION_SECURE_ID";
                    GlobalProperties::$SESSION_COOKIE_SECURE = true;

                }
            }else{
                // page accessed via http (nonsecure)
                switch($sslMode){
                    case 'ssl':
                        //enabled, but nonsecure allowed too.
                        break;
                    case 'ssl_only_paranoid':
                    case 'ssl_only':
                        throw new ProcessException(_("Nonsecure access is not enabled for this Wiki."));
                        break;

                }
            }

        // handle session at the begging of procession
        $runData->handleSessionStart();

        $template = $runData->getScreenTemplate();
        $classFile = $runData->getScreenClassPath();
        $className = $runData->getScreenClassName();
        $logger->debug("processing template: ".$runData->getScreenTemplate().", class: $className");

        require_once ($classFile);
        $screen = new $className ();

        // screen security check
        if(!$screen->isAllowed($runData)){
            if($classFile == $runData->getScreenClassPath()){
                $runData->setScreenTemplate("errors/NotAllowed");
            } else {
                // $screen->isAllowed() should set the error template!!! if not -
                // default NotAllowed is used

                // reload the class again - we do not want the unsecure screen to render!
                $classFile = $runData->getScreenClassPath();

                $className = $runData->getScreenClassName();
                $logger->debug("processing template: ".$runData->getScreenTemplate().", class: $className");
                require_once ($classFile);
                $screen = new $className ();
                $runData->setAction(null);
            }
        }

        // PROCESS ACTION

        $actionClass = $runData->getAction();
        $logger->debug("processing action $actionClass");
        while ($actionClass != null) {

            require_once (PathManager :: actionClass($actionClass));
            $tmpa1 = explode('/', $actionClass);
            $actionClassStripped = end($tmpa1);

            $action = new $actionClassStripped();

            $classFile = $runData->getScreenClassPath();
            if(!$action->isAllowed($runData)){
                if($classFile == $runData->getScreenClassPath()){
                    $runData->setScreenTemplate("errors/NotAllowed");
                }
                // $action->isAllowed() should set the error template!!! if not -
                // default NotAllowed is used
                break;

            }

            $actionEvent = $runData->getActionEvent();
            if ($actionEvent != null) {
                $action-> $actionEvent ($runData);
                $logger->debug("processing action: $actionClass, event: $actionEvent");
            } else {
                $logger->debug("processing action: $actionClass");
                $action->perform($runData);
            }
            // this is in case action changes the action name so that
            // the next action can be executed.
            if ($runData->getNextAction() != null) {
                $actionClass = $runData->getNextAction();
                $runData->setAction($actionClass);
                $runData->setActionEvent($runData->getNextActionEvent());
            } else {
                $actionClass = null;
            }
        }

        // end action process

        // check if template has been changed by the action. if so...
        if($template != $runData->getScreenTemplate){
            $classFile = $runData->getScreenClassPath();
            $className = $runData->getScreenClassName();
            $logger->debug("processing template: ".$runData->getScreenTemplate().", class: $className");

            require_once ($classFile);
            $screen = new $className ();
        }

        $rendered = $screen->render($runData);

        if ($rendered != null) {
            $moduleProcessor = new ModuleProcessor($runData);
             $moduleProcessor->setJavascriptInline(true); // embed associated javascript files in <script> tags
             $moduleProcessor->setCssInline(true);
             $rendered = $moduleProcessor->process($rendered);

        }

        $runData->handleSessionEnd();

        // one more thing - some url will need to be rewritten if using HTTPS
        if($_SERVER['HTTPS']){
            // ?
            // scripts
            $rendered = preg_replace(';<script(.*?)src="'.GlobalProperties::$HTTP_SCHEMA . "://" . GlobalProperties::$URL_HOST_PREG .'(.*?)</script>;s', '<script\\1src="https://' . GlobalProperties::$URL_HOST .'\\2</script>', $rendered);
            $rendered = preg_replace(';<link(.*?)href="'.GlobalProperties::$HTTP_SCHEMA . "://" . GlobalProperties::$URL_HOST_PREG .'(.*?)/>;s', '<link\\1href="https://' . GlobalProperties::$URL_HOST .'\\2/>', $rendered);
            $rendered = preg_replace(';(<img\s+.*?src=")http(://' . GlobalProperties::$URL_HOST_PREG .'(.*?)/>);s', '\\1https\\2', $rendered);
            do{
                $renderedOld = $rendered;
                $rendered = preg_replace(';(<style\s+[^>]*>.*?@import url\()http(://' . GlobalProperties::$URL_HOST_PREG .'.*?</style>);si', '\\1https\\2', $rendered);

            }while($renderedOld != $rendered);
        }

        echo $rendered;

    }

}
