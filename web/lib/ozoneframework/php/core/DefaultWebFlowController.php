<?php

namespace Ozone\Framework;



use Wikidot\Utils\GlobalProperties;
use Wikijump\Helpers\LegacyTools;

/**
 * Default web flow controller.
 *
 */
class DefaultWebFlowController extends WebFlowController {

	public function process() {
		global $timeStart;

		// initialize logging service
		$logger = OzoneLogger::instance();
		$loggerFileOutput = new OzoneLoggerFileOutput();
		$loggerFileOutput->setLogFileName(WIKIJUMP_ROOT."/logs/ozone.log");
		$logger->addLoggerOutput($loggerFileOutput);
		$logger->setDebugLevel(GlobalProperties::$LOGGER_LEVEL);

		$logger->debug("request processing started, logger initialized");

		Ozone ::init();

		$runData = new RunData();
		$runData->init();
		Ozone :: setRunData($runData);
		$logger->debug("RunData object created and initialized");

		// handle session at the begging of procession
		$runData->handleSessionStart();

		$template = $runData->getScreenTemplate();
		$classFile = $runData->getScreenClassPath();
		$class = LegacyTools::getNamespacedClassFromPath($runData->getScreenClassPath());
		$logger->debug("processing template: ".$runData->getScreenTemplate().", Class: $class");

		require_once ($classFile);
		$screen = new $class();

		// screen security check
		if(!$screen->isAllowed($runData)){
			if($classFile == $runData->getScreenClassPath()){
				$runData->setScreenTemplate("errors/NotAllowed");
			} else {
				// $screen->isAllowed() should set the error template!!! if not -
				// default NotAllowed is used

				// reload the Class again - we do not want the unsecure screen to render!
				$classFile = $runData->getScreenClassPath();

				$class = LegacyTools::getNamespacedClassFromPath($runData->getScreenClassPath());
				$logger->debug("processing template: ".$runData->getScreenTemplate().", Class: $class");
				require_once ($classFile);
				$screen = new $class();
				$runData->setAction(null);
			}
		}

		$logger->info("Ozone engines successfully initialized");

		// caching of LAYOUT tasks should start here
		$cacheSettings = $screen->getScreenCacheSettings();
		$updateLayoutContentLater = false;
		if($runData->getRequestMethod() == "GET" && $runData->getAction() == null && $cacheSettings != null && $cacheSettings->isLayoutCacheable($runData)){
			$content = ScreenCacheManager::instance();
			if($content != null && $content != ""){
				// process modules!!!
				// process modules...
	 			$moduleProcessor = new ModuleProcessor($runData);
	 			$out = $moduleProcessor->process($content);
				echo $out;

				$runData->handleSessionEnd();

				return;
			} else {
				$updateLayoutContentLater = true;
			}
		}

		// PROCESS ACTION

		$actionClass = $runData->getAction();
		$logger->debug("processing action $actionClass");
		while ($actionClass != null) {

			require_once (PathManager :: actionClass($actionClass));
			$class = LegacyTools::getNamespacedClassFromPath(PathManager :: actionClass($actionClass));
			$action = new $class();

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
			$class = LegacyTools::getNamespacedClassFromPath($runData->getScreenClassPath());
			$logger->debug("processing template: ".$runData->getScreenTemplate().", Class: $class");

			require_once ($classFile);
			$screen = new $class();
		}

		$rendered = $screen->render($runData);

		if ($rendered != null) {
			// process modules...
	 		$moduleProcessor = new ModuleProcessor($runData);
	 		$out = $moduleProcessor->process($rendered);
		}

		$runData->handleSessionEnd();

		echo $out;

	}

}
