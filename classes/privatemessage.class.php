<?php
class PrivateMessage extends EavAdjList {
	protected static function subGetClass() {
		return 'privatemessage';
	}
	protected static function subGetAttribs() {
		return array(	1=>	array( 'CREATOR',	static::ATTRIB_TYPE_INT ,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'BODY',		static::ATTRIB_TYPE_STRING,
												static::ATTRIB_PROP_UNIQUE ),
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
		$id = static::eav_create($userCfg['subject'], $userCfg['mailbox'], $type);
		if(!$id || $id < 1) {
			Error::generate('debug', '!$id || $id < 1 in PrivateMessage::Create');
			return false;
		}
		foreach($userCfg as $attrib => $val) {
			switch(strtoupper($attrib)) {
			case 'MAILBOX':
			case 'TYPE':
			case 'SUBJECT':
				continue 2; // switch is considered a looping structure for some reason
			case 'BODY':
				$storeval = $val; // nl2br ?
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
	public static function GetWithID($id) {
		$ret = static::get_with_id($id);
		$attr = static::get_all_attribs($id);
		foreach($attr as $vi) {
			$ret[strtolower($vi[0])] = $vi[1];
		}
		return $ret;
	}
	public static function GetSubject($id) {
		$ret = static::get_with_id($id);
		return $ret['subject'];
	}
	public static function GetTimestamp($id) {
		$ret = static::get_with_id($id);
		return $ret['creation_timestamp'];
	}
}
