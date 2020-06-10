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


use DB\ContactPeer;

class AccountBackContactsModule extends AccountBaseModule{

    public function build($runData){

        $user = $runData->getUser();

        // get all contacts
        $c = new Criteria();
        $c->add("contact.target_user_id", $user->getUserId());
        $c->addJoin("user_id", "ozone_user.user_id");
        $c->addOrderAscending("ozone_user.nick_name");

        $contacts = ContactPeer::instance()->select($c);

        $runData->contextAdd("contacts", $contacts);

    }
}
