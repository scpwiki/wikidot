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


class FlickrCheckModule extends SmartyModule {

    public function build($runData){
        $flickrId = $runData->getParameterList()->getParameterValue("flickr_id");
        $secret = $runData->getParameterList()->getParameterValue("secret");
        $flickr = FlickrHandler::instance();

        $photo = $flickr->photos_getInfo($flickrId, $secret);
        if($photo == null){
            $runData->contextAdd("nophoto", true);
            return;
        }
        $src = $flickr->buildPhotoURL($photo, "thumbnail");
        $runData->contextAdd("flickrId", $flickrId.$secret);
        $runData->contextAdd("photoSrc", $src);
        $runData->contextAdd("photo", $photo);
    }
}
