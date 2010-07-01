<?php
class User {
	const ATTRIB_TYPE_STRING	= 0;
	const ATTRIB_TYPE_INT		= 1;
	const ATTRIB_PROP_NONE		= 0;
	const ATTRIB_PROP_NODISPLAY	= 1; // for passwords and whatnot.
	//const ATTRIB_PROP_INTERNAL	= 2; // cannot be provided to User::Create
	private static $__inited = false;
	private static $ATTRIBUTES = null; // modify User::Init function
	private static $ROLES = null; // modify User::Init function
	private function get_role_id($rolestr) {
		$ret = 0;
		$roles = explode(',',$rolestr);
		for($i=0; $i < count(self::$ROLES); $i++)
			for($j=0; $j < count($roles); $j++)
				if(self::$ROLES[$i][0] == strtoupper($roles[$j]))
					$ret |= self::$ROLES[$i][1];
		return intval($ret);
	}
	private function get_role_str($roleid) {
		$res = array();
		for($i=0; $i < count(self::$ROLES); $i++)
			if(self::$ROLES[$i][1] & $roleid)
				array_push($res, self::$ROLES[$i][0]);
		if(count($res) == 0)
			return 'NONE';
		else
			return implode(',', $res);
	}
	private function get_attrib_id($attribstr) {
		for($i=0; $i < count(self::$ATTRIBUTES); $i++)
			if(self::$ATTRIBUTES[$i][0] == strtoupper($attribstr))
				return intval($i);
		return false;
	}
	private function get_attrib_str($attribid) {
		if($attribid < 0 || $attribid > count(self::$ATTRIBUTES))
			return false;
		else
			return self::$ATTRIBUTES[$attribid][0];
	}
	private function get_attrib_type($attribid) {
		if($attribid < 0 || $attribid > count(self::$ATTRIBUTES))
			return false;
		else
			return intval(self::$ATTRIBUTES[$attribid][1]);
	}
	private function get_attrib_props($attribid) {
		if($attribid < 0 || $attribid > count(self::$ATTRIBUTES))
			$ret = false;
		else
			$ret = self::$ATTRIBUTES[$attribid][2];
		return intval($ret);
	}
	private function create_user($name) {
		$res = db_query("INSERT INTO users (name) VALUES ('%s')",
						$name);
		if($res) {
			return mysql_insert_id();
		} else {
			Error::generate('notice', 'Username already taken.');
			return false;
		}
	}
	private function store_user_attrib($id, $attribstr, $val) {
		$attribid = self::get_attrib_id($attribstr);
		$attribtype = self::get_attrib_type($attribid);
		$attribprops = self::get_attrib_props($attribid);
		switch($attribtype) {
		case self::ATTRIB_TYPE_STRING:
			$datacol = 'stringdata';
			break;
		case self::ATTRIB_TYPE_INT:
			$datacol = 'intdata';
			break;
		default:
			Error::generate('debug', "Bad attribute type in store_user_attrib($id, $attribstr, $val)");
			return false;
		}
		db_query(	"REPLACE INTO user_data (id, attrib, %s) VALUES ('%d', '%d', '%s')",
					$datacol, $id, self::get_attrib_id($attribstr), $val);
		if(mysql_affected_rows() < 1) {
			Error::generate('debug', "Could not store user attribute as string: (id=$id, attrib=$attribstr, val=$val)");
			return false;
		}
	}
	private function get_user_attrib($id, $attribid) {
		$attribtype = self::get_attrib_type($attribid);
		switch($attribtype) {
		case self::ATTRIB_TYPE_STRING:
			$datacol = 'stringdata';
			break;
		case self::ATTRIB_TYPE_INT:
			$datacol = 'intdata';
			break;
		default:
			Error::generate('debug', "Bad attribute type in store_user_attrib($id, $attribstr, $val)");
			return false;
		}

		$res = db_query("SELECT %s FROM user_data WHERE id='%d' AND attrib='%d'",
						$datacol, $id, $attribid);
		if( !$res || !($ret = db_get_result($res)) ) {
			Error::generate('debug', 'No result, or could not query database in User::get_user_attrib');
			return false;
		}
		switch($attribtype) {
		case self::ATTRIB_TYPE_INT:
			$ret = intval($ret);
			break;
		case self::ATTRIB_TYPE_STRING:
		default:
			break;
		}
		return $ret;
	}
	private function get_user_attribs($id) {
		$res = db_query("SELECT attrib FROM user_data WHERE id='%d'",
						$id);
		if( !$res || !($ret = db_get_list_result($res, array('attr'))) ) {
			Error::generate('debug', 'Could not query database in User::get_user_attribs');
			return array();
		}
		foreach($ret as $key=>$val)
			$ret[$key] = intval($val['attrib']);
		
		return $ret;
	}
	private function get_user_id($name) {
		$res = db_query("SELECT id FROM users WHERE name='%s'",
						$name);
		if( !$res || !($ret = db_get_result($res)) ) {
			Error::generate('debug', 'Could not query database in User::get_user_id');
			return false;
		} else {
			return intval($ret);
		}
	}
	/**
		Public Functions
	*/
	// Because PHP can't handle non-trivial expressions in a static definition :(
	function Init() {
		if($__inited) return;
		else $__inited = true;
		if(is_null(self::$ATTRIBUTES)) {
			self::$ATTRIBUTES =
				array(	1=>	array( 'NAME',		self::ATTRIB_TYPE_STRING, self::ATTRIB_PROP_NONE ),
							array( 'EMAIL',		self::ATTRIB_TYPE_STRING, self::ATTRIB_PROP_NONE ),
							array( 'ROLE',		self::ATTRIB_TYPE_INT	, self::ATTRIB_PROP_NONE ),
							array( 'PASSWORD',	self::ATTRIB_TYPE_STRING, self::ATTRIB_PROP_NODISPLAY ) );
		}
		if(is_null(self::$ROLES)) {
			self::$ROLES =
				array(	array( 'USER',		1 ),
						array( 'ADMIN',		2 ) );
		}
	}
	function Create($userCfg) {
		$id = User::create_user($userCfg['name']);
		if($id < 0) {
			Error::generate('notice', 'Username already taken.');
			return false;
		}
		foreach($userCfg as $attrib => $val) {
			switch($attrib) {
			case 'role':
				$storeval = self::get_role_id($val);
				break;
			case 'password':
				$storeval = hash('sha256', $val);
				break;
			default:
				$storeval = $val;
			}
			User::store_user_attrib($id, $attrib, $storeval);
		}
		return $id;
	}
	function ListAll() {
		$res = db_query("SELECT * FROM users ORDER BY creation_timestamp");
		$ret = array();
		if(!$res) {
			Error::generate('debug', 'Could not query database in User::ListAll');
			return array();
		}
		return db_get_list_result($res, array('id', 'name', 'creation_timestamp'));
	}
	function GetAttrib($id, $attrib) {
		if(is_int($attrib)) {
			$attribid = $attrib;
		} else {
			$attribid = self::get_attrib_id($attrib);
		}
		if($attribid && !(self::get_attrib_props($attribid) & ATTRIB_PROP_NODISPLAY)) {
			$ret = self::get_user_attrib($id, $attribid);
			switch(strtoupper(self::get_attrib_str($attribid))) {
			case 'ROLE':
				$ret = self::get_role_str($ret);
				break;
			}
		} else {
			$ret = false;
		}
		return $ret;
	}
	function GetAttribs($id) {
		$attribs = User::get_user_attribs($id);
		$ret = array();
		foreach($attribs as $attrib) {
			$attrval = self::GetAttrib($id, $attrib);
			if($attrval) $ret[User::get_attrib_str($attrib)] = $attrval;
		}
		return $ret;
	}
	// Returns true on success, false on failure
	function Authenticate($name, $password) {
		$id = self::get_user_id($name);
		if(!$id) {
			Error::generate('debug', 'Invalid username/password combination');
			return false;
		}
		$correctpasshash = self::get_user_attrib($id, 'password');
		if($correctpasshash == hash('sha256', $password)) {
			if(session_id() == "" || !isset($_SESSION)) {
				Error::generate('debug', 'Authentication passed, but session did not exist');
				return false;
			} else {
				$_SESSION['userid'] = $id;
				return true;
			}
		} else {
			Error::generate('debug', 'Invalid username/password combination');
			return false;
		}
	}
	function Deauthenticate() {
		if(session_id() != "" && isset($_SESSION))
			$_SESSION['userid'] = false;
	}
	function IsAuthenticated() {
		if(session_id() != "" && isset($_SESSION) && $_SESSION['userid'])
			return true;
		else
			return false;
	}
	function HasPermissions($role) {
		if(!self::IsAuthenticated())
			return false;
		return self::get_user_attrib($_SESSION['userid'], 'role') & get_role_id($role);
	}
}
?>
