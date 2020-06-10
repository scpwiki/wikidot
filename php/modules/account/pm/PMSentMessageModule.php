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


use DB\PrivateMessagePeer;

class PMSentMessageModule extends AccountBaseModule {

    public function build($runData){

        $userId = $runData->getUserId();
        $pl = $runData->getParameterList();
        $messageId = $pl->getParameterValue("message_id");

        $message = PrivateMessagePeer::instance()->selectByPrimaryKey($messageId);
        if($message->getFromUserId() != $userId){
            throw new ProcessException(_("Error selecting message."), "no_message");
        }

        $runData->contextAdd("message", $message);

        // get next & previous message
        $messageId = $message->getMessageId();
        $c = new Criteria();
        $c->add("from_user_id", $userId);
        $c->add("message_id", $messageId, ">");
        $c->add("flag", 1);
        $c->addOrderAscending("message_id");

        $newerMessage = PrivateMessagePeer::instance()->selectOne($c);

        $c = new Criteria();
        $c->add("from_user_id", $userId);
        $c->add("message_id", $messageId, "<");
        $c->add("flag", 1);
        $c->addOrderDescending("message_id");

        $olderMessage = PrivateMessagePeer::instance()->selectOne($c);

        $runData->contextAdd("newerMessage", $newerMessage);
        $runData->contextAdd("olderMessage", $olderMessage);

    }

}
