<?php
class File extends EAV {
	protected static function subGetClass() {
		return 'file';
	}
	protected static function subGetAttribs() {
		return array(	1=>	array( 'NAME',		static::ATTRIB_TYPE_STRING,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'PATH',		static::ATTRIB_TYPE_STRING,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'OWNER',		static::ATTRIB_TYPE_INT ,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'ROLES',		static::ATTRIB_TYPE_INT,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'TYPE',		static::ATTRIB_TYPE_STRING,
												static::ATTRIB_PROP_UNIQUE ),
							array( 'COMMENT',	static::ATTRIB_TYPE_STRING,
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
	public static function Create($userCfg, $uploadPath) {
		global $ROOT;
		$id = static::eav_create($userCfg['name']);
		if($id < 1) { // pretty sure this shouldn't happen with current schema
			Error::generate('notice', 'Filename already taken.');
			return false;
		}
		// Copy the file from the uploadPath to the given userCfg['path']
		if(!isset($userCfg['path']) || !move_uploaded_file($uploadPath, "$ROOT/".$userCfg['path'])) {
			Error::generate('debug', 'Could not move file');
			return false;
		}
		foreach($userCfg as $attrib => $val) {
			switch(strtoupper($attrib)) {
			default:
				$storeval = $val;
			}
			static::store_attrib($id, $attrib, $storeval);
		}
		return $id;
	}
	public static function ListAll() {
		$res = db_query("SELECT * FROM files ORDER BY creation_timestamp");
		$ret = array();
		if(!$res) {
			Error::generate('debug', 'Could not query database in file::ListAll');
			return array();
		}
		return db_get_list_result($res, array('id', 'name', 'creation_timestamp'));
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
}
?>
