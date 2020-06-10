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
 * @package Ozone
 * @version $Id$
 * @copyright Copyright (c) 2008, Wikidot Inc.
 * @license http://www.gnu.org/licenses/agpl-3.0.html GNU Affero General Public License
 */

// define autoload paths
$opath = OZONE_ROOT;
$apath = WIKIDOT_ROOT;

$classpath = array();

$classpath[] = $opath."/php/core/database";
$classpath[] = $opath."/php/core";
$classpath[] = $opath."/php/core/exceptions";
$classpath[] = $apath."/php/utils";
$classpath[] = $apath."/php/db";
$classpath[] = $apath."/php/db/base";
$classpath[] = $apath."/php/class";
$classpath[] = $apath."/php/pingback";
$classpath[] = $apath."/conf";
$classpath[] = $apath."/lib/zf/library";

$GLOBALS['classpath'] = $classpath;

$paths = explode(PATH_SEPARATOR, get_include_path());
$paths = array_merge($paths, $classpath);
$paths = array_unique($paths);
$paths = implode(PATH_SEPARATOR, $paths);
set_include_path($paths);

/**
 * Function responsible for including .php files containing class definitions.
 * @param string $className name of the class
 */
/* spl_autoload_register( function($className) {
    trigger_error("Autoloading ".$className);
    $className = str_replace('\\','/', $className);
    include_once($className.'.php');
    $class_actual = explode('/',$className);
    if(! class_exists(end($class_actual)) && ! interface_exists(end($class_actual))) {
        trigger_error("Class $className not loaded.");
    }
    else { trigger_error("Loaded ".end($class_actual)); }
    return;
}); */

spl_autoload_register(function ($class) {
    if(GlobalProperties::$LOGGER_LEVEL == "debug") {
        trigger_error("Paths: " . get_include_path());
    }
            $file = str_replace('\\', DIRECTORY_SEPARATOR, $class).'.php';
            if (stream_resolve_include_path($file)) {
            require $file;
            if(GlobalProperties::$LOGGER_LEVEL == "debug") {
                trigger_error("Loaded $file for $class");
            }
                return true;
        }
        trigger_error("Failed to load $file for $class");
            return false;
        });
