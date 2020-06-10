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


class PasswordRecoveryModule extends SmartyModule {

    public function build($runData){
        $userId = $runData->getUserId();
        if($userId !== null){
            throw new ProcessException(_("You already are logged in."), "already_logged");
        }
        $runData->ajaxResponseAdd("key", CryptUtils::modulus());

        $runData->sessionStart();
        $seed = CryptUtils::generateSeed(10);
        $runData->sessionAdd("login_seed", $seed);
        $this->extraJs[] = '/common--javascript/crypto/rsa.js';

    }

}
