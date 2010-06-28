<?php
class User {
	private function create_user($name) {
		$res = database_query(sprintf(
			"SELECT COUNT(*) FROM users WHERE name='%s'",
			mysql_real_escape_string($name)));
		if(mysql_result($res, 0) < 1) {
			database_query(sprintf(
				"INSERT INTO users (name) VALUES ('%s')",
				mysql_real_escape_string($name)));
			return mysql_insert_id();
		} else {
			Error::generate('fatal', 'Username already taken.');
			return -1;
		}
	}
	private function store_user_attribute_as_string($id, $attrib, $val) {
		database_query(sprintf(
			"REPLACE INTO user_data (userid, attrib, stringdata) VALUES ('%d', '%s', '%s')",
			$id, $attrib, $val));
		if(mysql_affected_rows() < 1) {
			Error::generate('debug', "Could not store user attribute as string: (id=$id, attrib=$attrib, val=$val)");
			return -1;
		}
	}
	/**
		Static Functions
	*/
	function create($userCfg) {
		$id = create_user($userCfg['name']);
		if($id < 0) return -1;
		foreach($userCfg as $attrib => $val) {
			switch($attrib) {
			case 'email':
			case 'role':
				store_user_attrib_as_string($id, $attrib, $val);
				break;
			case 'password':
				store_user_attrib_as_string($id, $attrib, hash('sha256', $val));
				break;
			default:
				Error::generate('debug', 'Invalid user attribute in User::create');
			}
		}
		return $id;
	}
}
?>
