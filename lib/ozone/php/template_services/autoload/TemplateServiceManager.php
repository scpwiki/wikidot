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
 * @category Ozone
 * @package Ozone_Web
 * @version $Id$
 * @copyright Copyright (c) 2008, Wikidot Inc.
 * @license http://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License
 */


/**
 * TemplateServiceManager is a TemplateService for accessing
 * on-demand (not autoloaded) services.
 */
class TemplateServiceManager extends TemplateService{
    protected $serviceName = "serviceManager";
    private $runData;
    protected $storage = array();

    public function __construct($runData){
        $this->runData = $runData;
    }

    public function getService($className){
        if(isset($this->storage["$className"])){
            return     $this->storage["$className"];
        } else {
            require_once PathManager::ozonePhpServiceOnDemandDir().$className.".php";
            $instance = new $className($this->runData);
            $this->storage["$className"] = $instance;
            return $instance;
        }
    }

}
