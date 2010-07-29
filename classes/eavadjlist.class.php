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
		if(mysql_affected_rows() < 1) {
			Error::generate('debug', "Could not store attribute");
			return false;
		} else {
			return true;
		}
	}
	protected static function get_attrib($id, $attribid) {
		if(!is_int($attribid)) $attribid = static::get_attrib_id($attribid);
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
			Error::generate('debug', "Bad attribute type in get_attrib($id, $attribstr, $val)");
			return false;
		}

		$res = db_query("SELECT %s FROM %s_data WHERE id='%d' AND attrib='%d'",
						$datacol, static::subGetClass(), $id, $attribid);
		if( !$res ) {
			Error::generate('debug', 'No result, or could not query database in get_attrib');
			return false;
		}

		if($attribprops & static::ATTRIB_PROP_UNIQUE) {
			$ret = db_get_result($res);
		} else {
			$ret = db_get_list_of_results($res);
		}

		if($attribtype == static::ATTRIB_TYPE_INT && !is_array($ret)) {
			return intval($ret);
		} else {
			return $ret;
		}
	}
	protected static function get_attribs($id) {
		$res = db_query("SELECT attrib FROM %s_data WHERE id='%d'",
						static::subGetClass(), $id);
		if( !$res || !($ret = db_get_list_result($res, array('attr'))) ) {
			Error::generate('debug', 'Could not query database in get_attribs');
			return array();
		}
		foreach($ret as $key=>$val)
			$ret[$key] = intval($val['attrib']);
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
		$res = db_query( 'INSERT INTO %ss (subject, parent, type)
							VALUES (\'%s\', \'%d\', \'%d\')',
							static::subGetClass(), $subject, $parent, $type);
		if($res) {
			return mysql_insert_id();
		} else {
			Error::generate('notice', static::subGetClass().'name already taken.');
			return false;
		}
	}
	protected static function eav_list($id, $type=1) {
		$ret = db_query( '
			SELECT node.id, node.subject, node.creation_timestamp
				FROM		%ss AS node,
							%ss AS parent
				WHERE		node.parent = parent.id
					AND		parent.id	= \'%d\'
					AND		node.type	= \'%d\'
				ORDER BY	node.creation_timestamp DESC
			', static::subGetClass(), static::subGetClass(), $id, $type );
		if(!$ret) {
			Error::generate('debug', 'Could not query db in hierarchical eav list');
			return array();
		}
		$ret = db_get_list_result($ret);
		return $ret;
	}
}
