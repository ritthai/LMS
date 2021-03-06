<?php
abstract class EavAdjList {
	const ATTRIB_TYPE_STRING	= 0;
	const ATTRIB_TYPE_INT		= 1;
	const ATTRIB_PROP_NONE		= 0;
	const ATTRIB_PROP_NODISPLAY	= 1; // for passwords and whatnot.
	const ATTRIB_PROP_READONLY	= 2; // cannot be provided to User::SetAttrib
	const ATTRIB_PROP_UNIQUE	= 4;
	protected static $__inited		= false;
	protected static $ATTRIBUTES	= null; // modify User::Init function
	protected static $cache			= array();
	// Assume initialization, i.e.:
	//     Don't DELETE before a REPLACE/INSERT
	protected static $init_mode		= false;
	public static function enableInitMode() {
		static::$init_mode = true;
	}
	public static function disableInitMode() {
		static::$init_mode = false;
	}
	protected static function get_attrib_id($attribstr) {
		$attribs = static::subGetAttribs();
		for($i=1; $i < count(static::subGetAttribs())+1; $i++) {
			if($attribs[$i][0] == strtoupper($attribstr)) {
				return $i;
			}
		}
		return false;
	}
	protected static function get_attrib_str($attribid) {
		$attribs = static::subGetAttribs();
		if($attribid < 0 || $attribid > count(static::subGetAttribs())) {
			return false;
		} else {
			return $attribs[$attribid][0];
		}
	}
	protected static function get_attrib_type($attribid) {
		$attribs = static::subGetAttribs();
		if($attribid < 0 || $attribid > count(static::subGetAttribs())) {
			return false;
		} else {
			return intval($attribs[$attribid][1]);
		}
	}
	protected static function get_attrib_props($attribid) {
		$attribs = static::subGetAttribs();
		if($attribid < 0 || $attribid > count(static::subGetAttribs())) {
			$ret = false;
		} else {
			$ret = $attribs[$attribid][2];
		}
		return intval($ret);
	}
	protected static function store_attrib($id, $attrib, $val) {
		Error::generate('debug', "store_attrib($id, $attrib, $val)");
		if(is_int($attrib)) {
			$attribid = $attrib;
		} else {
			$attribid = static::get_attrib_id($attrib);
		}
		if(!$attribid) {
			Error::generate('debug', 'Attrib not found in store_attrib');
			return false;
		}
		$attribtype = static::get_attrib_type($attribid);
		$attribprops = static::get_attrib_props($attribid);
		switch($attribtype) {
		case static::ATTRIB_TYPE_STRING:
			$datacol = 'stringdata';
			break;
		case static::ATTRIB_TYPE_INT:
			$datacol = 'intdata';
			break;
		default:
			Error::generate('debug', "Bad attribute type in store_attrib($id, $attribstr, $val)");
			return false;
		}
		if($attribprops & static::ATTRIB_PROP_UNIQUE) {
			/*db_query(	"UPDATE %s_data SET %s='%s' WHERE id='%d' AND attrib='%d'",
						static::subGetClass(), $datacol, $val, $id, $attribid);*/
			if(static::$init_mode) {
				Error::generate('debug', 'Init mode removed query');
			} else {
				db_query(	"DELETE FROM %s_data WHERE id='%d' AND attrib='%d'",
							static::subGetClass(), $id, $attribid);
			}
		}// else {
			db_query(	"REPLACE INTO %s_data (id, attrib, %s) VALUES ('%d', '%d', '%s')",
						static::subGetClass(), $datacol, $id, $attribid, $val);
		//}
		if(db_affected_rows() < 1) {
			Error::generate('debug', "Could not store attribute");
			return false;
		} else {
			static::$cache[$id][$attribid] = $val;
			return true;
		}
	}
	protected static function get_attrib($id, $attribid) {
		if(!$id||$id==0) { Error::generate('debug', 'id is 0'); return false; }
		if(!is_int($attribid)) $attribid = static::get_attrib_id($attribid);
		if(!$attribid) {
			Error::generate('debug', 'Attrib not found in store_attrib');
			return false;
		}
		$attribtype = static::get_attrib_type($attribid);
		$attribprops = static::get_attrib_props($attribid);
		if(isset(static::$cache[$id][$attribid]) && $attribprops & static::ATTRIB_PROP_UNIQUE) return static::$cache[$id][$attribid];
		switch($attribtype) {
		case static::ATTRIB_TYPE_STRING:
			$datacol = 'stringdata';
			break;
		case static::ATTRIB_TYPE_INT:
			$datacol = 'intdata';
			break;
		default:
			Error::generate('debug', "Bad attribute type in get_attrib($id, $attribstr, $val)");
			return false;
		}

		if($attribprops & static::ATTRIB_PROP_UNIQUE) {
			$res = db_query("SELECT attrib,stringdata,intdata FROM %s_data WHERE id='%d'",
							static::subGetClass(), $id);
		} else {
			$res = db_query("SELECT %s FROM %s_data WHERE id='%d' AND attrib='%d'",
							$datacol, static::subGetClass(), $id, $attribid);
		}
		if( !$res ) {
			Error::generate('debug', 'No result, or could not query database in get_attrib');
			return false;
		}

		if($attribprops & static::ATTRIB_PROP_UNIQUE) {
			$ret = db_get_list_of_list_results($res);
			foreach($ret as $k=>$v) {
				static::$cache[$id][$v[0]] =
					static::get_attrib_type($v[0])=='stringdata' ? $v[1] : intval($v[2]);
			}
			$ret = static::$cache[$id][$attribid];
		} else {
			$ret = db_get_list_of_results($res);
			static::$cache[$id][$attribid] = $ret;
		}

		if($attribtype == static::ATTRIB_TYPE_INT && !is_array($ret)) {
			return intval($ret);
		} else {
			return $ret;
		}
	}
	protected static function get_all_attribs($id) {
		$attribs = static::get_attribs($id);
		$ret = array();
		foreach($attribs as $attrib) {
			$add = array(static::get_attrib_str($attrib), static::get_attrib($id, $attrib));
			if(is_array($add[1])) $ret = array_merge($ret, $add);
			else array_push($ret, $add);
		}
		return $ret;
	}
	protected static function get_attribs($id) {
		if(!$id||$id==0) { Error::generate('debug', 'id is 0'); return array(); }
		$res = db_query("SELECT attrib FROM %s_data WHERE id='%d'",
						static::subGetClass(), $id);
		if( !$res || !($ret = db_get_list_of_results($res)) ) {
			Error::generate('debug', 'Could not query database in get_attribs');
			return array();
		}
		$ret = array_map('intval', $ret);
		$ret = array_unique($ret, SORT_NUMERIC);
		return $ret;
	}
	protected static function get_id($name) {
		$res = db_query("SELECT id FROM %ss WHERE name='%s'",
						static::subGetClass(), $name);
		if( !$res || !($ret = db_get_result($res)) ) {
			Error::generate('debug', 'Could not query database in get_id');
			return false;
		} else {
			return intval($ret);
		}
	}
	protected static function get_with_id($name) {
		$res = db_query("SELECT * FROM %ss WHERE id='%s'",
						static::subGetClass(), $name);
		if( !$res || !($ret = db_get_assoc($res)) ) {
			Error::generate('debug', 'Could not query database in get_with_id');
			return false;
		} else {
			return $ret;
		}
	}
	protected static function eav_create($subject, $parent, $type=1) {
		$ret = false;
		$good = db_query( 'REPLACE INTO %ss (subject, parent, type)
							VALUES (\'%s\', \'%d\', \'%d\')',
							static::subGetClass(), $subject, $parent, $type);
		//$lock = (static::subGetClass() == 'comments' ||
				//db_query("INSERT INTO %ss_lock (locked) VALUES ('0')", static::subGetClass()));
		//$lock = (static::subGetClass() == 'comments');
		$lock = true; // TODO: Investigate and see if this could cause any problems
		if($good && $lock) {
			$ret = db_insert_id();
			db_query("INSERT INTO %ss_lock (locked) VALUES ('0')", static::subGetClass());
		} else {
			Error::generate('notice', static::subGetClass().' name already taken.');
			$ret = false;
		}
		return $ret;
	}
	protected static function eav_list($id, $type=1, $orderby=0) {
		if($orderby===0) $orderby = "ORDER BY node.creation_timestamp DESC";
		if(!$id||$id==0) { Error::generate('debug', 'id is 0'); return array(); }
		$ret = db_query( '
			SELECT node.*
				FROM		%ss AS node,
							%ss AS parent
				WHERE		node.parent = parent.id
					AND		parent.id	= \'%d\'
					AND		node.type	= \'%d\'
				%s
			', static::subGetClass(), static::subGetClass(), $id, $type, $orderby );
		if(!$ret) {
			Error::generate('debug', 'Could not query db in hierarchical eav list');
			return array();
		}
		//$ret = db_get_list_result($ret);
		$ret = db_get_list_assoc($ret);
		return $ret;
	}
}
