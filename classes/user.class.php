<?php
class User extends EAV {
	private static $ROLES		= null; // modify static::Init function
	private static $SALT		= 'g39gqks9023';
	private static $MODE		= '';
	protected static function subGetClass() {
		return 'user';
	}
	protected static function subGetAttribs() {
		return array(	1=>	array( 'NAME',		static::ATTRIB_TYPE_STRING,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'REALNAME',	static::ATTRIB_TYPE_STRING,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'UNIVERSITY',static::ATTRIB_TYPE_STRING,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'GRADYEAR',	static::ATTRIB_TYPE_STRING,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'EMAIL',		static::ATTRIB_TYPE_STRING,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'ROLE',		static::ATTRIB_TYPE_INT ,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'PASSWORD',	static::ATTRIB_TYPE_STRING,
												static::ATTRIB_PROP_NODISPLAY | static::ATTRIB_PROP_UNIQUE ),
							array( 'FORGOTN_PASS_RST_KEY',
												static::ATTRIB_TYPE_STRING,
												static::ATTRIB_PROP_NODISPLAY | static::ATTRIB_PROP_UNIQUE ),
							array( 'FORGOTN_PASS_TIMESTAMP',
												static::ATTRIB_TYPE_STRING,
												static::ATTRIB_PROP_NODISPLAY | static::ATTRIB_PROP_UNIQUE ),
							array( 'FILE',		static::ATTRIB_TYPE_INT,
												static::ATTRIB_PROP_NOSHOW ),
							array( 'TOPICFAV',	static::ATTRIB_TYPE_INT,
												static::ATTRIB_PROP_NONE ),
							array( 'COURSEFAV',	static::ATTRIB_TYPE_INT,
												static::ATTRIB_PROP_NONE ) );
	}
	protected static function subGetRoles() {
		// Note: Not all of these apply to a User.
		// Some apply to objects associated with a User, like a File.
		return array(	array( 'ANY',			0 ),
						array( 'ADMIN',			1 ),
						array( 'BANNED',		2 ) );
	}
	private static function get_role_id($rolestr) {
		if(is_int($rolestr)) return $rolestr;
		$ret = 0;
		$roles = explode(',',$rolestr);
		$all_roles = static::subGetRoles();
		for($i=0; $i < count($all_roles); $i++)
			for($j=0; $j < count($roles); $j++)
				if($all_roles[$i][0] == strtoupper($roles[$j]))
					$ret |= $all_roles[$i][1];
		return $ret;
	}
	private static function get_role_str($roleid) {
		$res = array();
		$all_roles = static::subGetRoles();
		for($i=0; $i < count($all_roles); $i++)
			if($all_roles[$i][1] & $roleid)
				array_push($res, $all_roles[$i][0]);
		if(count($res) == 0) {
			return 'NONE';
		} else {
			return implode(',', $res);
		}
	}
	/**
		Public Functions
	*/
	public static function enterStatusMode() {
		static::$MODE = 'status';
	}
	public static function leaveStatusMode() {
		static::$MODE = '';
	}
	public static function Init() {
		if($__inited) return;
		else $__inited = true;
		if(is_null(static::subGetRoles())) {
		}
	}
	public static function Create($userCfg) {
		$id = static::eav_create($userCfg['name']);
		if($id < 1) {
			Error::generate('notice', 'Username already taken.');
			return false;
		}
		foreach($userCfg as $attrib => $val) {
			switch(strtoupper($attrib)) {
			case 'ROLE':
				$storeval = static::get_role_id($val);
				break;
			case 'PASSWORD':
				$storeval = hash('sha256', $SALT.$val);
				break;
			default:
				$storeval = $val;
			}
			static::store_attrib($id, $attrib, $storeval);
		}
		return $id;
	}
	public static function DeleteAttrib($uid, $attrib, $val=false) {
		if(is_int($attrib)) {
			$attribid = $attrib;
		} else {
			$attribid = static::get_attrib_id($attrib);
		}
		$id = static::delete_attrib($uid, $attribid, $val);
		if(!$id) {
			Error::generate('debug', 'Could not delete.');
			return false;
		} else {
			return true;
		}
	}
	public static function ListAll() {
		$res = db_query("SELECT * FROM users ORDER BY creation_timestamp");
		$ret = array();
		if(!$res) {
			Error::generate('debug', 'Could not query database in static::ListAll');
			return array();
		}
		return db_get_list_result($res, array('id', 'name', 'creation_timestamp'));
	}
	public static function SetAttrib($id, $attrib, $val) {
		if(is_int($attrib)) {
			$attribid = $attrib;
		} else {
			$attribid = static::get_attrib_id($attrib);
		}
		if($attribid && !(static::get_attrib_props($attribid) & static::ATTRIB_PROP_READONLY)) {
			switch(strtoupper(static::get_attrib_str($attribid))) {
			case 'ROLE':
				$storeval = static::get_role_id($val);
				break;
			case 'PASSWORD':
				$storeval = static::_hash($val);
				break;
			default:
				$storeval = $val;
			}
			$ret = static::store_attrib($id, $attribid, $storeval);
		} else {
			$ret = false;
		}
		return $ret;
	}
	public static function GetAttrib($id, $attrib) {
		if(is_int($attrib)) {
			$attribid = $attrib;
		} else {
			$attribid = static::get_attrib_id($attrib);
		}
		if($attribid && !(static::get_attrib_props($attribid) & static::ATTRIB_PROP_NODISPLAY)) {
			if(static::get_attrib_props($attribid) & static::ATTRIB_PROP_NOSHOW
				&& static::$MODE == 'status')
			{
				return false;
			}
			$ret = static::get_attrib($id, $attribid);
			switch(strtoupper(static::get_attrib_str($attribid))) {
			case 'ROLE':
				$ret = static::get_role_str($ret);
				break;
			}
		} else {
			$ret = false;
		}
		return $ret;
	}
	public static function GetAttribs($id) {
		return static::get_all_attribs($id);
	}
	public static function GetUserID($name) {
		return static::get_id($name);
	}
	public static function GetAuthenticatedAttrib($attr) {
		if(!static::IsAuthenticated()) {
			return false;
		} else {
			return static::GetAttrib(static::GetAuthenticatedID(), $attr);
		}
	}
	// Returns true on success, false on failure
	public static function Authenticate($name, $password, &$error) {
		$id = static::get_id($name);
		if(!$id) {
			Error::generate('debug', 'Invalid username/password combination in static::Authenticate (bad id)');
			$error = 'Invalid username/password combination.';
			return false;
		}
		$correctpasshash = static::get_attrib($id, 'password');
		$role = static::get_attrib($id, static::get_attrib_id('role'));
		if($correctpasshash == static::_hash($password)) {
			if(session_id() == "" || !isset($_SESSION)) {
				Error::generate('debug', 'Authentication passed, but session did not exist in static::Authenticate');
				$error = 'A problem occured. Please contact an administrator.';
				return false;
			} else if($role && $role & static::get_role_id('banned')) {
				if($error === null) {
					Error::generate('notice', 'This account has been banned.');
				} else {
					$error = 'This account has been banned.';
				}
				return false;
			} else {
				$_SESSION['userid'] = $id;
				return true;
			}
		} else {
			if($error === null) {
				Error::generate('debug', 'Invalid username/password combination in static::Authenticate (bad pass)');
			} else {
				$error = 'Invalid username/password combination.';
			}
			
			return false;
		}
	}
	public static function ForceAuthenticate() {
		$_SESSION['userid'] = 1;
	}
	public static function Deauthenticate() {
		if(session_id() != "" && isset($_SESSION) && $_SESSION['userid']) {
			$_SESSION['userid'] = false;
			return true;
		} else {
			return false;
		}
	}
	public static function IsAuthenticated() {
		if(session_id() != "" && isset($_SESSION) && isset($_SESSION['userid']) && $_SESSION['userid'] > 0) {
			return true;
		} else {
			return false;
		}
	}
	public static function HasPermissions($role) {
		$fail = false;
		// $role == false | 0 = 'any'
		if(!$role || !static::get_role_id($role)) {
			return true;
		}
		if(!static::IsAuthenticated()) {
			Error::generate('debug', 'Not authenticated in HasPermissions in static::Authenticate');
			return false;
		}
		return (!$fail && 
				(static::get_attrib($_SESSION['userid'], static::get_attrib_id('role'))
					& static::get_role_id($role))
				!= 0);
	}
	public static function GetAuthenticatedID() {
		if(!static::IsAuthenticated()) {
			Error::generate('debug', 'Not authenticated in GetAuthenticatedID in static::Authenticate');
			return false;
		}
		return $_SESSION['userid'];
	}
	private static function _hash($pass) {
		return hash('sha256', $SALT.$pass);
	}
	public static function GenerateForgottenPasswordKey($name) {
		$id = static::get_id($name);
		if(!$id) {
			Error::generate('notice', 'Invalid username.');
			return false;
		}
		$key = $id.'_'.hash('sha256', uniqid('hvs29tr1'.mt_rand()));
		static::store_attrib($id, 'FORGOTN_PASS_RST_KEY', $key);
		static::store_attrib($id, 'FORGOTN_PASS_TIMESTAMP', time());
		return $key;
	}
	public static function ValidateForgottenPasswordKey($key) {
		$id = intval(substr($key, 0, strpos($key, '_')));
		$stored_key = static::get_attrib($id, static::get_attrib_id('FORGOTN_PASS_RST_KEY'));
		$stored_timestamp = static::get_attrib($id, static::get_attrib_id('FORGOTN_PASS_TIMESTAMP'));
		if($key != $stored_key) {
			Error::generate('notice', 'The URL you have followed is invalid.');
			return false;
		}
		if((time() - $stored_timestamp)/(60*60*24) > 1) { // 1 day expiration
			Error::generate('notice', 'The URL you have followed is expired.');
			return false;
		}
		return $id;
	}
	public static function AuthenticatedUserHasMatchingAttrib($attr, $val) {
		$attribs = static::GetAttribs(static::GetAuthenticatedID());
		foreach($attribs as $attrib) {
			if(!strcasecmp($attrib[0], $attr) && $attrib[1] == $val) {
				return true;
			}
		}
		return false;
	}
	public static function GetAvatar($id) {
		$defaultAvatar = "/images/navigation/bulbasaur.png";
		$avatar = File::ListAll($id, 2);
		if($avatar) return '/file/get?id='.$avatar[0]['id'];
		else return $defaultAvatar;
	}
}

