<?php
class Comment extends EavAdjList {
	/*
		Known types:
			1: Comment
			2: Topic
	*/
	protected static function subGetClass() {
		return 'comment';
	}
	protected static function subGetAttribs() {
		return array(	1=>	array( 'OWNER',		static::ATTRIB_TYPE_INT ,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'SUBJECT',	static::ATTRIB_TYPE_STRING,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'BODY',		static::ATTRIB_TYPE_STRING,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'TYPE',		static::ATTRIB_TYPE_INT,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'ID',		static::ATTRIB_TYPE_INT,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'RATING',	static::ATTRIB_TYPE_INT,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'SOURCE',	static::ATTRIB_TYPE_STRING,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'LINK',		static::ATTRIB_TYPE_STRING,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'TITLE',		static::ATTRIB_TYPE_STRING,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'URL',		static::ATTRIB_TYPE_STRING,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'CATEGORY',	static::ATTRIB_TYPE_STRING,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'SRC',		static::ATTRIB_TYPE_STRING,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'THUMBNAIL_URL',
												static::ATTRIB_TYPE_STRING,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'THUMBNAIL_WIDTH',
												static::ATTRIB_TYPE_INT,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'THUMBNAIL_HEIGHT',
												static::ATTRIB_TYPE_INT,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'ART',		static::ATTRIB_TYPE_STRING,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'VOTE',		static::ATTRIB_TYPE_INT,
												static::ATTRIB_PROP_NONE ),
					);
	}
	/**
		Public Functions
	*/
	public static function Init() {
		if($__inited) return;
		else $__inited = true;
	}
	public static function Create($userCfg) {
		($type = $userCfg['type']) or $type = 1;
		$id = static::eav_create($userCfg['subject'], $userCfg['id'], $type);
		if(!$id || $id < 1) {
			Error::generate('debug', '!$id || $id < 1 in Comment::Create');
			return false;
		}
		foreach($userCfg as $attrib => $val) {
			switch(strtoupper($attrib)) {
			case 'ID':
			case 'TYPE':
			case 'SUBJECT':
				continue 2; // switch is considered a looping structure for some reason
			case 'BODY':
				$storeval = nl2br($val);
				break;
			default:
				$storeval = $val;
			}
			static::store_attrib($id, $attrib, $storeval);
		}
		return $id;
	}
	public static function ListAll($id=1, $type=1) {
		Error::generate('debug', "Comment::ListAll($id, $type)");
		$ret = static::eav_list($id, $type);
		if(!$ret) $ret = array();
		return array_reverse($ret, true);
	}
	public static function GetAttrib($id, $attrib) {
		if(is_int($attrib)) {
			$attribid = $attrib;
		} else {
			$attribid = static::get_attrib_id($attrib);
		}
		if($attribid && !(static::get_attrib_props($attribid) & static::ATTRIB_PROP_NODISPLAY)) {
			$ret = static::get_attrib($id, $attribid);
		} else {
			$ret = false;
		}
		return $ret;
	}
	public static function SetAttrib($id, $attrib, $val) {
		if(is_int($attrib)) {
			$attribid = $attrib;
		} else {
			$attribid = static::get_attrib_id($attrib);
		}
		if($attribid && !(static::get_attrib_props($attribid) & static::ATTRIB_PROP_READONLY)) {
			switch(strtoupper(static::get_attrib_str($attribid))) {
			default:
				$storeval = $val;
			}
			$ret = static::store_attrib($id, $attribid, $storeval);
		} else {
			$ret = false;
		}
		return $ret;
	}
	public static function GetAttribs($id) {
		return static::get_all_attribs($id);
	}
	public static function GetSubject($id) {
		$ret = static::get_with_id($id);
		return $ret['subject'];
	}
	public static function GetTimestamp($id) {
		$ret = static::get_with_id($id);
		return $ret['creation_timestamp'];
	}
	public static function CanVote($uid, $cid) {
		$res = db_query("
			SELECT count(*) FROM comment_data
				WHERE	attrib='%d'
					AND	id='%d'
					AND	intdata='%d'
			", static::get_attrib_id('vote'), $cid, $uid);
		if(!$res || ($ret = db_get_result($res)) === false) {
			return false;
		} else {
			return ($ret === 0);
		}
	}
	public static function Vote($uid, $cid, $val) {
		$res1 = db_query("
			INSERT IGNORE INTO comment_data (id, attrib, intdata)
				VALUES ('%d', '%s', '%d')
			", static::get_attrib_id('vote'), ''.$uid, $cid);
		if(!$res1 || db_affected_rows() != 1) {
			return false;
		} else {
			$res2 = db_query("INSERT IGNORE INTO comment_data (id, attrib, intdata) VALUES ('%d', '%d', '%d')",
							$cid, static::get_attrib_id('rating'), 0);
			$res2 = db_query("UPDATE comment_data SET intdata = intdata + %d WHERE attrib='%d' AND id='%d'",
							intval($val), static::get_attrib_id('rating'), $cid);
			return ($res2 && db_affected_rows() == 1);
		}
	}
}
