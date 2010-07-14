<?php
class Comment extends EavAdjList {
	protected static function subGetClass() {
		return 'comment';
	}
	protected static function subGetAttribs() {
		return array(	1=>	array( 'OWNER',		static::ATTRIB_TYPE_INT ,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'SUBJECT',	static::ATTRIB_TYPE_STRING,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'BODY',		static::ATTRIB_TYPE_STRING,
												static::ATTRIB_PROP_UNIQUE )
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
		$id = static::eav_create($userCfg['subject'], $userCfg['id']);
		if(!$id || $id < 1) {
			Error::generate('debug', '!$id || $id < 1 in Comment::Create');
			return false;
		}
		foreach($userCfg as $attrib => $val) {
			switch(strtoupper($attrib)) {
			case 'ID':
				continue;
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
	public static function ListAll($id=1) {
		$ret = static::eav_list($id);
		if(!$ret) $ret = array();
		return $ret;
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
		$attribs = static::get_attribs($id);
		$ret = array();
		foreach($attribs as $attrib) {
			$attrval = static::GetAttrib($id, $attrib);
			if($attrval) {
				if(is_array($attrval)) {
					foreach($attrval as $v) {
						array_push($ret, array(static::get_attrib_str($attrib), $v[0]));
					}
				} else {
					array_push($ret, array(static::get_attrib_str($attrib), $attrval));
				}
			}
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
?>
