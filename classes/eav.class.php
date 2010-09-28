<?php
abstract class EAV {
	const ATTRIB_TYPE_STRING	= 0;
	const ATTRIB_TYPE_INT		= 1;
	const ATTRIB_PROP_NONE		= 0;
	const ATTRIB_PROP_NODISPLAY	= 1; // for passwords and whatnot.
	const ATTRIB_PROP_READONLY	= 2; // cannot be provided to User::SetAttrib
	const ATTRIB_PROP_UNIQUE	= 4;
	const ATTRIB_PROP_NOSHOW	= 8; // don't show on status page
	protected static $__inited		= false;
	protected static $ATTRIBUTES	= null; // modify User::Init function
	protected static $cache			= array();
	protected static function get_attrib_id($attribstr) {
		$attribs = static::subGetAttribs();
		for($i=1; $i < count(static::subGetAttribs())+1; $i++) {
			if($attribs[$i][0] == strtoupper($attribstr))
				return $i;
		}
		return false;
	}
	protected static function get_attrib_str($attribid) {
		$attribs = static::subGetAttribs();
		if($attribid < 0 || $attribid > count(static::subGetAttribs()))
			return false;
		else
			return $attribs[$attribid][0];
	}
	protected static function get_attrib_type($attribid) {
		$attribs = static::subGetAttribs();
		if($attribid < 0 || $attribid > count(static::subGetAttribs()))
			return false;
		else
			return intval($attribs[$attribid][1]);
	}
	protected static function get_attrib_props($attribid) {
		$attribs = static::subGetAttribs();
		if($attribid < 0 || $attribid > count(static::subGetAttribs()))
			$ret = false;
		else
			$ret = $attribs[$attribid][2];
		return intval($ret);
	}
	protected static function eav_create($name) {
		$res = db_query("INSERT INTO %ss (name) VALUES ('%s')",
						static::subGetClass(), $name);
		if($res) {
			return db_insert_id();
		} else {
			Error::generate('notice', static::subGetClass().'name already taken.');
			return false;
		}
	}
	protected static function store_attrib($id, $attrib, $val) {
		Error::generate('debug', "store_attrib($id, $attrib, $val)");
		if(is_int($attrib)) {
			$attribid = $attrib;
		} else {
			$attribid = static::get_attrib_id($attrib);
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
			db_query(	"DELETE FROM %s_data WHERE id='%d' AND attrib='%d'",
						static::subGetClass(), $id, $attribid);
		}
		db_query(	"REPLACE INTO %s_data (id, attrib, %s) VALUES ('%d', '%d', '%s')",
					static::subGetClass(), $datacol, $id, $attribid, $val);
		if(db_affected_rows() < 1) {
			Error::generate('debug', "Could not store attribute");
			return false;
		} else {
			static::$cache[$id][$attribid] = $val;
			return true;
		}
	}
	protected static function delete_attrib($id, $attrib, $val=false) {
		Error::generate('debug', "delete_attrib($id, $attrib, $val)");
		if(is_int($attrib)) {
			$attribid = $attrib;
		} else {
			$attribid = static::get_attrib_id($attrib);
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
		if($val) {
			$val = db_real_escape_string($val);
			$valconstraint = "AND $datacol='$val'";
		} else {
			$valconstraint = '';
		}
		db_query("DELETE FROM %s_data WHERE attrib='%d' AND id='%d' $valconstraint",
						static::subGetClass(), $attribid, $id);
		if(db_affected_rows() >= 1) {
			static::$cache[$id][$attribid] = false;
			return true;
		} else {
			Error::generate('debug', 'could not delete attribute');
			return false;
		}
	}
	protected static function get_attrib($id, $attribid) {
		if(!$id || $id==0) { Error::generate('debug', 'id is 0'); return false; }
		if(!is_int($attribid)) $attribid = static::get_attrib_id($attribid);
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
			$res = db_query("SELECT attrib,stringdata,intdata,options FROM %s_data WHERE id='%d'",
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
		if(!$id || $id==0) { Error::generate('debug', 'id is 0'); return array(); }
		$res = db_query("SELECT attrib FROM %s_data WHERE id='%d'",
						static::subGetClass(), $id);
		if( !$res || !($ret = db_get_list_of_results($res)) ) {
			Error::generate('debug', 'Could not query database in get_attribs');
			return array();
		}
		foreach($ret as $key=>$val)
			$ret[$key] = intval($val[0]);
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
}
?>
