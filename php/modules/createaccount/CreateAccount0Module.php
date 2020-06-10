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


class CreateAccount0Module extends SmartyModule {

    public function isAllowed($runData){
        if($runData->getUserId() !== null){
            throw new ProcessException(_("You are already logged in. Why would you want to create a new account?"), "logged_in");
        }

        $rstep = $runData->sessionGet("rstep");
        return true;
    }

    public function build($runData){

        $code =  $runData->sessionGet('captchaCode');

        $runData->ajaxResponseAdd("key", CryptUtils::modulus());

        if($code === null){
            srand((double)microtime()*1000000);
            $string = md5(rand(0,9999));
            $code = substr($string, 2, 4);
            $code = str_replace('0','O', $code);
            $code = strtoupper($code);
            $runData->sessionAdd("captchaCode", $code);
        }

        $runData->contextAdd("rand", rand(0,1000));

        $runData->sessionAdd("rstep", 0);
        $this->extraJs[] = '/common--javascript/crypto/rsa.js';
    }

}
