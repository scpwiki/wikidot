<?php

namespace Wikidot\QuickModules;

use Ozone\Framework\Database\Database;
use Ozone\Framework\QuickModule;

class MemberLookupQModule extends QuickModule {

	public function process($data){
		// does not use data
		$siteId = $_GET['siteId'];

		$search = $_GET['q'];
		if($search == null || strlen($search) ==0) return;

		$search1 = pg_escape_string(preg_quote($search));
		$search2 = pg_escape_string($search);

		Database::init();
		$q1 = "SELECT users.username AS name, users.id FROM users, member WHERE username" .
				" ~* '^$search1' AND username != '$search2' AND member.site_id='".pg_escape_string($siteId)."' " .
						"AND member.user_id = users.id ";
		$q1 .= "ORDER BY username LIMIT 20";
		$q2 = "SELECT users.username AS name, users.id FROM users, member WHERE username" .
				" = '$search2' AND member.site_id='".pg_escape_string($siteId)."' " .
						"AND member.user_id = users.id ";
		$db = Database::connection();

		$result1 = $db->query($q1);
		$result1 = $result1->fetchAll();
		$result2 = $db->query($q2);
		$result2 = $result2->fetchAll();

		if($result1 == null && $result2 != null) $result = $result2;
		if($result2 == null && $result1 != null) $result = $result1;
		if($result1 == null && $result2 == null) $result = false; // NOT null since it breakes autocomplete!!!
		if($result1 != null && $result2 != null){
			$result = array_merge($result2, $result1);
		}

		return array('users' => $result);
	}

}
